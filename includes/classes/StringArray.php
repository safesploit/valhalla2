<?php
class StringArray
{

    /* 
    *   Comment Strings
    */
    public function commentStatusSuccessful()
    {
        return "<p>Commented Posted!</p>";
    }

    public function commentStatusEmptySubmit()
    {
        return "<b><p>Cannot submit an empty comment!</p></b>";
    }

    public function postComment($postId)
    {
        return "
                <form action='comment_frame.php?post_id=$postId' id='comment_form' name='postComment$postId' method='POST'>
                    <textarea name='post_body' placeholder='Add a comment'></textarea>
                    <input type='submit' name='postComment$postId' value='Post'>
                </form>
                ";
    }


}

?>