<?php
    /*
    Plugin Name: Delete All Comments Easily
    Plugin URI: https://abcofwp.com/
    Description: Plugin to delete all comments or all Pending comments
    Author: J P S
    Version: 1.3
    Author URI: https://abcofwp.com/
    */
?>
<?php
    function dce_main() 
    {
        global $wpdb;
	    $c_all = $wpdb->query("SELECT comment_ID FROM $wpdb->comments");
            $c_pend = $wpdb->query("SELECT comment_ID FROM $wpdb->comments WHERE comment_approved = '0'");
?>

    <div class="wrap">
	    <h2>Delete All Comments or All Pending Comments Easily</h2>

        <?php if($_POST['c_all'] == 'a' && $_POST['all'] == 'a')
        {
            if($wpdb->query("DELETE FROM $wpdb->comments") != FALSE)
	            {
                    $wpdb->query("DELETE FROM $wpdb->comments");
                    echo "<p style='color:green'><strong>All comments have been deleted.</strong></p>";
                }
            else 
                {echo "<p style='color:red'><strong>Something Went Wrong,Please Try Again!</strong></p>";}
        }
        else {
        ?>
        <?php echo "<h4>Number of all Comments : " . $c_all . " </h4>" ; ?>
        
        <?php if($c_all > 0) { ?>
            <form name="dce" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                <input type="hidden" name="c_all" value="a">
                <input type="checkbox" name="all" value="a" /> Delete all comments
                <p class="submit">
		            <input type="submit" name="Submit" value="Delete all Comments" />
                </p>
            </form>
        <?php 
        } 
        else 
        {
            echo "<p><strong>All comments have been deleted.</strong></p>" ;
        }
?>
<?php if($_POST['c_pend'] == 'p' && $_POST['pend'] == 'p')
        {
            if($wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = '0'") != FALSE)
	            {
                    $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = '0'");
                    echo "<p style='color:green'><strong>All Pending comments have been deleted.</strong></p>";
                }
            else 
                {echo "<p style='color:red'><strong>Something Went Wrong,Please Try Again!</strong></p>";}
        }
        else {
        ?>
        <?php echo "<h4>Number of Pending Comments : " . $c_pend . " </h4>" ; ?>
        
        <?php if($c_pend > 0) { ?>
            <form name="dcep" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                <input type="hidden" name="c_pend" value="p">
                <input type="checkbox" name="pend" value="p" /> Delete all pending comments
                <p class="submit">
		            <input type="submit" name="Submit" value="Delete all pending Comments" />
                </p>
            </form>
        <?php 
        } 
        else 
        {
            echo "<p><strong>All pending comments have been deleted.</strong></p>" ;
        }
   }
?>
<h4>Warning : Once Comment Deleted can't be restored!</h4>
    </div> 
<?php
        } 
    }
?>
<?php
 add_action('admin_menu', 'dce_menu');
    function dce_menu()
    {
		     add_management_page("Delete All Comments Easily","Delete All Comments Easily","administrator","dce","dce_main" );
    }
?>
