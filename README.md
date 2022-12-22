# Valhalla2
Valhalla 2 is a full scale social media website! Not only does it contain all the main functionality you get with the likes of Facebook or Twitter, but it also respects privacy!

# Features
  - Email and password login
  - User settings page
  - Each user has their own profile page
  - Mutual friends list when checking another profile page
  - Likes and comments on posts
  - Submit posts, comments and likes through AJAX
  - Submit images as posts
  - Messaging system
  - Dropdown menus with badge icon for messages, notification and friend requests
  - Live search
  - YouTube links are converted from 'watch' to 'embed'
  - Trend words

# Table of Contents

- [Valhalla2](#valhalla2)
- [Features](#features)
- [Table of Contents](#table-of-contents)
- [Setup and Usage](#setup-and-usage)
  - [Compatability](#compatability)
  - [Dependencies](#dependencies)
  - [SQL Database Structure](#sql-database-structure)
  - [LAMP Setup (Production)](#lamp-setup-production)
  - [Connecting PHP to MySQL Server](#connecting-php-to-mysql-server)
  - [Webserver Rewrite](#webserver-rewrite)
- [Programming Logic](#programming-logic)
  - [Invite System](#invite-system)
  - [Security Features](#security-features)
- [Preview Images](#preview-images)
  - [Registration page](#registration-page)
  - [Settings page for Administrator showing to generate invite code](#settings-page-for-administrator-showing-to-generate-invite-code)
  - [Settings page for a 'regular' user](#settings-page-for-a-regular-user)
  - [Uploading a profile image](#uploading-a-profile-image)
  - [Making a post on a user feed](#making-a-post-on-a-user-feed)
  - [Friend requests](#friend-requests)
  - [Post preview on feed](#post-preview-on-feed)
  - [Pending notifications](#pending-notifications)
  - [Checking notifications](#checking-notifications)
  - [Check Instant Message](#check-instant-message)
  - [Check mutual friends](#check-mutual-friends)
  - [Trending Words](#trending-words)
  - [Embedded YouTube Videos](#embedded-youtube-videos)
  - [Live search from anypage showing mutual friends](#live-search-from-anypage-showing-mutual-friends)

# Setup and Usage

## Compatability

Developed in PHP 7.4.

PHP 8.1 seems to be incompatible as of Valhalla 2.1.1.

## Dependencies
PHP extensions required:

- GD (for image cropping)
- MySQL (for database connection)

## SQL Database Structure

The database empty tables can be from `valhalla2-sql-tables.sql`.

## LAMP Setup (Production)

The production setup for serversis still udner development.

### Apache Setup

`/etc/apache2/apache.conf` needs to be edited to include the following:

    <Directory /var/www/Valhalla2>
        Options Indexes FollowSymLinks Includes ExecCGI
        AllowOverride All
        Require all granted
    </Directory>

### PHP Setup

`/etc/php/7.4/apache2/php.ini` needs to be edited to include the following:

    ;;;;;;;;;;;;;;;;
    ; File Uploads ;
    ;;;;;;;;;;;;;;;;
    file_uploads = On
    upload_max_filesize = 30M

    post_max_size = 8M


### MySQL Server Setup

    safesploit@sws-vm05:~$ mysql -u root -p
    Enter password: 
    ...
    mysql>

### SQL User Creation

Amend the password _PASSWORD_HERE_ using a strong [random password](https://passwordsgenerator.net/).

If you are using a database server not hosted locally, change 'localhost' to your IP address.

    mysql> CREATE USER IF NOT EXISTS 'valhalla'@'localhost' IDENTIFIED BY 'PASSWORD_HERE';

### SQL User Permissions

The SQL user 'valhalla' must have SELECT, INSERT, UPDATE and DELETE privileges:

    mysql> GRANT SELECT, INSERT, UPDATE, DELETE ON `valhalla2`.* TO 'valhalla'@'localhost';

  - INSERT is used primarily for messages, notifications and post.
  - SELECT is required for the website to return queries.
  - UPDATE is required to amend posts which are marked as `deleted`.
  - DELETE is required for deleting private messages.

## Connecting PHP to MySQL Server

In the file `config/config.php` the following must be entered correctly for your database configuration:

    $dbname = "valhalla2";
    $dbhost = "localhost";
    $dbuser = "valhalla";
    $dbpass = "PASSWORD_HERE";

In the file `valhalla2-sql-table-No-Data.sql` the database will be created as `valhalla2`.

## Webserver Rewrite

User profiles are rewritten from `/profile.php?safesploit` to `/safesploit`.

This rewrite relies on Apache having the `mod-rewrite` module being enabled and the `.htaccess` file being present with the configuration below.

    RewriteEngine On
    RewriteRule ^([a-zA-Z0-9_-]+)$ profile.php?profile_username=$1
    RewriteRule ^([a-zA-Z0-9_-]+)/$ profile.php?profile_username=$1

    RewriteEngine On
    Options +FollowSymLinks
    RewriteCond %{THE_REQUEST} ^.*/index.php
    RewriteRule ^(.*)index.php$ /$1 [R=301,L]

The `valhalla2-setup-env.sh` script will generate the `.htaccess` file.

### Apache Rewrite

    sudo a2enmod rewrite

Apache's instance will need to be reloaded afterwards

    sudo service apache2 reload

### Creating Rewrite Rules
This step is not necessary if using `valhalla2-setup-env.sh`

--

Inside the web directory `/var/www/Valhalla2/` create `.htaccess` with the configuration above.

- Not included because GitHub forbids .htaccess uploads.
- Maybe upload as htaccess.zip and extract.
- I have the intention of doing the same for `search.php`

### Running valhalla2-setup-env.sh

Never blindly run a Bash script with root privileges from the Internet!

    cd ~/Downloads/Valhalla2/
    sudo sh setup-env.sh



# Programming Logic

## Invite System

Within the `includes/form_handlers/settings_handler.php` there is code for invite code generation. This table in the database is checked when a user registers.

I have since disabled this requirement as of `v2.1.X`, but the feature can be enable again, by uncommented in `includes/form_handlers/register_handler.php`.

	if($user_obj->inviteCodeCheck($invite_code) == False)
	  array_push($error_array, $errInviteCodeInvalid);

Likewise, the HTML form must be uncommented in `register.php`.

    <input type="text" name="reg_invite_code" placeholder="Invite Code (optional)" value="" autocomplete="off"> 

## Security Features

### Each user has a unique salt

While database compromises via SQL injection would prefer to be avoid completely. Realisitically this attack vector needs to be consider. 
In response I create another table in the database called 'salts' this table contains two fields 'username' and 'salt'.
Each user on registration has their own salt randomly generated.
This ensures the hashed password stored for 'userA' will never be the same as 'userB' even if both users have the same cleartext password.
Hence, rendering rainbow table attacks not impossible against this platform.
The salt table is accessed during registration, login and _when a user is logged-in under the settings page_. Otherwise, the attack surface against this table is nonexistent.



### PBKDF2 Implementation

As the key derivative function I chose PBKDF2. 
Inside the `Salt.php` file
`$hash = hash_pbkdf2("sha256", $password, $salt, $iterations, $length);`

- $iterations = 100000
- $length = 32

Meanwhile `$password` and `$salt` are variable.

#### Salt

Inspecting `includes/classes/Salt.php` the following functions can be seen:

- `hashPassword()`
- `generateSalt()`
- `getSalt()`
- `submitSalt()`


### Login with email address**

Email login is security through obsecurity, but has been proven to aid security against bruteforce attacks. 
Habbo Hotel ([for financial crime](https://youtu.be/HiDPTiFHfcs?t=1157)) saw many victims of brute forcing using username/password for authentication as opposed to email/password.




# Preview Images
## Registration page
<img width="792" alt="001_register" src="https://user-images.githubusercontent.com/10171446/153286193-dd8308bb-604d-4fdd-a8bf-49f8f87d2840.PNG">


## Settings page for Administrator showing to generate invite code
<img width="791" alt="003_settingsPage_Admin" src="https://user-images.githubusercontent.com/10171446/153286091-18211e7e-de46-4da8-855d-7ad88d847358.PNG">


## Settings page for a 'regular' user
<img width="791" alt="004_settingsPage" src="https://user-images.githubusercontent.com/10171446/153286296-867a794e-68d0-43f5-b3d4-6cd15404a9f3.PNG">

## Uploading a profile image
<img width="806" alt="007_uploadProfilePic_cropping" src="https://user-images.githubusercontent.com/10171446/153286403-3d8f278f-bb0c-4050-954e-b17b3b5fc74c.PNG">

## Making a post on a user feed
<img width="806" alt="014_MakingAPostOnSomeoneElses_profilePage" src="https://user-images.githubusercontent.com/10171446/153286747-ca5089f0-1c61-4e75-8a6f-e8149c95fd71.PNG">

## Friend requests
<img width="806" alt="018_friendRequests" src="https://user-images.githubusercontent.com/10171446/153286959-ae62ad1e-2e31-470d-9b68-0aee55f6a63d.PNG">



## Post preview on feed
<img width="806" alt="015_successfulPost" src="https://user-images.githubusercontent.com/10171446/153286768-df83b914-3106-47c1-bd39-106e4dfecfb7.PNG">

## Pending notifications
<img width="806" alt="016_notificationsPending" src="https://user-images.githubusercontent.com/10171446/153286833-622a0b7b-3f72-44b0-be55-f1b695642f3a.PNG">

## Checking notifications
<img width="806" alt="021_profileNotifications" src="https://user-images.githubusercontent.com/10171446/153287270-4503484f-4246-4602-be2b-5f47c2adbe65.PNG">

## Check Instant Message

<img width="806" alt="023_privateMessage" src="https://user-images.githubusercontent.com/10171446/153287347-c98dfc1e-4ac1-49fd-a9fa-d6910f6c8650.PNG">

<img width="806" alt="024_privateMessageReply" src="https://user-images.githubusercontent.com/10171446/153287414-de721796-5468-47d8-a693-15ec8c49cccf.PNG">

## Check mutual friends

<img width="806" alt="025_mutualFriends" src="https://user-images.githubusercontent.com/10171446/153287527-a4d8da71-8c8b-49a3-85e5-669cd88d9bb3.PNG">

<img width="806" alt="026_mutualFriendsCount" src="https://user-images.githubusercontent.com/10171446/153287536-e0f86d6a-a179-4dbf-9403-252b1cde2fe9.PNG">

## Trending Words

<img width="806" alt="027_TrendingWords" src="https://user-images.githubusercontent.com/10171446/153287606-dd06cbcf-1efe-403e-96c1-745d52880d89.PNG">



## Embedded YouTube Videos
![011_ProfilePage_postsomething_embeddedYT](https://user-images.githubusercontent.com/10171446/153286511-4e13bd7d-183e-452c-9230-99a7724cf2b2.PNG)

## Live search from anypage showing mutual friends
<img width="806" alt="012_liveSearch" src="https://user-images.githubusercontent.com/10171446/153286632-61761292-44ce-47d6-bb7d-6ac13241fdf7.PNG">



<img width="806" alt="014_MakingAPostOnSomeoneElses_profilePage" src="https://user-images.githubusercontent.com/10171446/153285760-3cafabe6-1e46-4c48-a01d-1666bb834c80.PNG">
