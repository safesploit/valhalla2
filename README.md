# Valhalla2
Valhalla 2.0 is a full scale social media website! Not only does it contain all the main functionality you get with the likes of Facebook or Twitter, but it also respects privacy!

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

- [Configuration and Setup](#configuration-and-setup)
  - [Importing SQL Database Structure](#importing-sql-database-structure)
- [Security Features](#security-features)
- [Preview](#preview)
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

# Configuration and Setup

## Importing SQL Database Structure

valhalla2-sql-table-No-Data.sql

# Security Features

**Each user has a unique salt**

While database compromises via SQL injection would prefer to be avoid completely. Realisitically this attack vector needs to be consider. 
In response I create another table in the database called 'salts' this table contains two fields 'username' and 'salt'.
Each user on registration has their own salt randomly generated.
This ensures the hashed password stored for 'userA' will never be the same as 'userB' even if both users have the same cleartext password.
Hence, rendering rainbow table attacks not impossible against this platform.
The salt table is accessed during registration, login and _when a user is logged-in under the settings page_. Otherwise, the attack surface against this table is nonexistent.



**PBKDF2**

PBKDF2 has been deployed on this site.



**Invite only**

A validinvite code must be used during registration. 
Ensuring users not authorised are not able use the website, users not logged-in are redirected to the login page.


**Login with email address**

This is security through obsecurity, but has been proven to aid security against bruteforce attacks. 
Which Haboo saw many victims of due to it using username/password for authentication as opposed to email/password.




# Preview
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
