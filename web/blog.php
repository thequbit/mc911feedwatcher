<?php
	require_once("_header.php");
?>

			
				<?
				
					require_once("Database.class.php");
					require_once("BlogPost.class.php");
				
					$db = new Database();
					
					// get all of the blog posts
					$blogosts = $db->GetAllBlogPosts();
					
					foreach($blogposts as $blogpost)
					{
						echo '<div class="blogwrapper">';
						
						echo '<div class="blogcreationdatetime">';
						echo $blogposts->creationdate;
						echo '</div>';
						
						echo '<div class="blogtitle">';
						echo $blogposts->title;
						echo '</div>';
						
						echo '<div class="blogbody">';
						echo $blogposts->body;
						echo '</div>';
						
						echo '</div>';
					}
				
				?>
			
<?php
	require_once("_footer.php");
?>