<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header('Location: login.php');
    exit;
}

$userType = $_SESSION['user_type']; //to redirect user to user specific dashboard after successful update of post
if (empty($userType)) {
    header('Location: index.php'); // Redirect to index.php if user type is not set
    exit;
}


// Retrieve blog post data for editing
if (isset($_GET['postId'])) {
    $postId = $_GET['postId'];
    $stmt = $db->prepare("SELECT blogTitle, blogContent FROM blog WHERE id = :postId");
    $stmt->bindParam(':postId', $postId);
    $stmt->execute();
    $blogPost = $stmt->fetch(PDO::FETCH_ASSOC);
    $blogTitle = $blogPost['blogTitle'];
    $blogContent = $blogPost['blogContent'];

    // Display the edit form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Blog Post</title>
        <link rel="stylesheet" href="styles.css">
        <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
        }
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            resize: vertical;
        }
        .update-blog-title{
            padding-top: 5px;
            padding-bottom: 20px;
        }
        #blogTitle{
            padding-top: 4px;
            padding-bottom: 4px;
        }
        .update-button{
            display: inline-block;
            font-size: 16px;
            padding: 8px 10px;
            border: 1px solid #1ea5c3;
            border-radius: 5px;
            background-color: #1ea5c3;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .back-button {
            font-size: 16px;
            display: inline-block;
            padding: 8px 10px;
            border: 1px solid #1ea5c3;
            border-radius: 5px;
            background-color: transparent;
            color: #1ea5c3;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-left: 10px;
        }
        input[type="submit"]:hover,
        .back-button:hover {
            background-color: #dbd9d9;
            border-color: #fff;
            color: #1ea5c3;
        }
    </style>
    </head>
    <body>
        <div class="container">
            <h1>Edit Your Blog Post</h1>
            <form method="post" action="edit_blog.php">
                <label for="blogTitle">Title:</label><br>
                <div class="update-blog-title">
                <input type="text" id="blogTitle" name="blogTitle" value="<?php echo htmlspecialchars($blogTitle); ?>"><br>
                </div>
                <label for="blogContent">Blog Content:</label><br>
                <textarea id="blogContent" name="blogContent" rows="8"><?php echo htmlspecialchars($blogContent); ?></textarea><br>

                <input type="hidden" name="postId" value="<?php echo $postId; ?>">
                <input class="update-button" type="submit" value="Update">
                <button class="back-button" onclick="goBack()">Back</button>
            </form>
        </div>
    </body>
    </html>

    <?php
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['blogContent']) && isset($_POST['postId']) && isset($_POST['blogTitle'])) {
    // Update blog post content in the database
    $postId = $_POST['postId'];
    $blogTitle = htmlspecialchars($_POST['blogTitle']);
    $blogContent = htmlspecialchars($_POST['blogContent']);

    $stmt = $db->prepare("UPDATE blog SET blogTitle = :blogTitle, blogContent = :blogContent WHERE id = :postId");
    $stmt->bindParam(':blogTitle', $blogTitle);
    $stmt->bindParam(':blogContent', $blogContent);
    $stmt->bindParam(':postId', $postId);
    $stmt->execute();

    $_SESSION['success_message'] = "Blog post updated successfully.";
  // Redirect to the appropriate dashboard based on user type
  switch ($userType) {
    case 'Student':
        header('Location: student_dashboard.php');
        break;
    case 'Lecturer':
        header('Location: lecturer_dashboard.php');
        break;
    case 'Alumni':
        header('Location: alumni_dashboard.php');
        break;
    default:
        header('Location: index.php'); 
        break;
}
exit;
} else {
    // Redirect if postId is not provided or request method is not POST
    header('Location: index.php');
    exit;
}
?>
