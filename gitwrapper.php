<?php

if (!isset($_REQUEST["dir"]) || $_REQUEST["dir"] == "")
    $screen = "main";
else if (isset($_REQUEST["commit"]) && $_REQUEST["commit"] != "")
    $screen = "commit_diff";
else if (isset($_REQUEST["branch_1"]) && $_REQUEST["branch_1"] != "")
    $screen = "commit_list";
else
    $screen = "branch_pick";

?>

<style>
#main-content {
    font-family: Open Sans;
    font-size: 24px;
    font-style: normal;
    font-weight: 400;
    line-height: 50px;
    letter-spacing: 0px;
    text-align: center;
}

.main-screen {
    position: absolute;
    left: 50%;
    transform: translate(-50%, 0px);
    top: 30%;
    width: 600px;
}

.title {
    color: #000000;
    mix-blend-mode: normal;
    opacity: 0.77;
    margin-bottom: 7%;
}

.terminal_view {
    background-color: black;
    height: 100vh;
}

.terminal_text {
    background-color:black; 
    font-size:16px; 
    font-family:Ubuntu Mono; 
    padding:5px;
}
</style>
<script>
function to_main()
{
    window.location.search = "";
}

function to_branch_pick()
{
    window.location.search = "dir=" + document.getElementById('git_dir').value;
}

function branch_switch()
{
    branch_1 = document.getElementById('branch_1').value;
    document.getElementById('branch_1').value = document.getElementById('branch_2').value;
    document.getElementById('branch_2').value = branch_1;
}

function to_commit_list(dir)
{
    url = "dir=" + dir;
    url += "&branch_1=" + document.getElementById('branch_1').value;
    url += "&branch_2=" + document.getElementById('branch_2').value;
    window.location.search = url;
}
</script>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <title> <?php 
            if (isset($_REQUEST["commit"]) && $_REQUEST["commit"] != null)
                echo $_REQUEST["commit"] . " | ";
            else if (isset($_REQUEST["branch_1"]) && $_REQUEST["branch_1"] != null)
                echo $_REQUEST["branch_1"] . " | ";
            else if (isset($_REQUEST["dir"]) && $_REQUEST["dir"] != null)
                echo $_REQUEST["dir"] . " | ";
            echo "GIT BROWSER";
        ?> </title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&lang=en" />
        <link rel="preconnect" href="https://fonts.gstatic.com"> 
        <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Mono&display=swap" rel="stylesheet"> 
    </head>

    <body>

<!-- 
    main screen
    - starting point of app
    - used to input directory of git repo (not counting the /.git part)
-->
<?php if ($screen == "main") { ?>
    <div id="main-content">
        <div class="main-screen">
            <p class="title"> GIT Browser </p>
            <p style="text-align:left;margin-left:20px;">Git directory:</p>
            <div>
                <span class = "col-md-11">
                    <input id="git_dir" class="form-control" style="min-height:50px;text-align:right;font-size:20px;" type="text" placeholder="git repo directory">
                </span>
                <span class = "col-md-1" style="margin-left:-20px;font-size:20px;">/.git</span>
            </div>
            <button class="btn btn-default btn btn-primary" style="font-size:20px;margin-top:20px;"
                onclick="to_branch_pick();">Next</button>
        </div>
    </div>
    <script>
        // Detect 'enter' key in directory input
        document.getElementById("git_dir").addEventListener("keyup", function(event) 
        {
            if (event.key == "Enter") 
            {
                to_branch_pick();
            }
        });
    </script>
<?php } ?>


<!-- 
    branch pick
    - after main
    - used to input branches to be compared, target branch is master by default
-->
<?php if ($screen == "branch_pick") { ?>
    <div id="main-content">
        <div class="main-screen">
            <p class="title"> Compare branch to target </p>
            <p style="text-align:left;margin-left:5px;margin-top:-10px;">Git branch:</p>
            <input id="branch_1" class="form-control" style="min-height:50px;font-size:20px;" autoComplete="on" list="suggestions" value="" placeholder="branch-name">
            <button class="btn btn-default btn" style="font-size:25px;margin-top:15px;"
                title="switch branches" onclick="branch_switch();"><  ></button>
            <p style="text-align:left;margin-left:5px;margin-top:0px;">Target branch (e.g. master):</p>
            <input id="branch_2" class="form-control" style="min-height:50px;font-size:20px;" autoComplete="on" list="suggestions" value="master" placeholder="master">
            <button class="btn btn-default btn" style="font-size:20px;margin-top:20px;"
                onclick="to_main();">Back</button>
            <button class="btn btn-default btn btn-primary" style="font-size:20px;margin-top:20px;"
                onclick="to_commit_list('<?= $_REQUEST["dir"] ?>');">Next</button>
        </div>
    </div>
    <datalist id="suggestions">
        <?php
        $git_dir = "--git-dir {$_REQUEST["dir"]}/.git";
        $git = "git $git_dir branch";
        exec($git, $output);

        foreach ($output as $output_line)
        {
            $output_line = trim($output_line, "* "); // Remove active branch tag
            echo "<option>$output_line</option>";
        }
        ?>
    </datalist>
    <script>
        // Detect 'enter' key in branch pick inputs
        document.getElementById("branch_1").addEventListener("keyup", function(event) 
        {
            if (event.key == "Enter") 
            {
                to_commit_list('<?= $_REQUEST["dir"] ?>');
            }
        });
        document.getElementById("branch_2").addEventListener("keyup", function(event) 
        {
            if (event.key == "Enter") 
            {
                to_commit_list('<?= $_REQUEST["dir"] ?>');
            }
        });
    </script>
<?php } ?>


<!-- 
    commit list
    - after branch pick
    - list all found commits, commit can be clicked to show commit diff screen
-->
<?php if ($screen == "commit_list") { ?>
    <div class="terminal_view">
    <?php
        $git_dir = "--git-dir {$_REQUEST["dir"]}/.git";

        $source = $_REQUEST["branch_1"];
        
        // Default to master if no other target branch picked
        if (isset($_REQUEST["branch_2"]) && $_REQUEST["branch_2"] != "")
            $target = $_REQUEST["branch_2"];
        else
            $target = "master";

        $git = "git $git_dir log --pretty=format:'%h==%cr==%an==%s' --abbrev-commit $source ^$target";

        exec($git, $output);
        if (empty($output))
            die();
           
        foreach ($output as $output_line)
            handle_log($output_line);
    ?>
    </div>
<?php } ?>


<!-- 
    commit diff
    - after commit list
    - show diff for certain commit
-->
<?php if ($screen == "commit_diff") { ?>
    <div class="terminal_view">
    <?php
        $git_dir = "--git-dir {$_REQUEST["dir"]}/.git";

        $commit = $_REQUEST["commit"];

        $git = "git $git_dir show --format=medium --abbrev-commit $commit";

        exec($git, $output);

        if (empty($output))
            die();

        handle_commit($output);
    ?>
    </div>
<?php } ?>

    </body>
</html>


<?php
function handle_log($line)
{
    $line = htmlspecialchars($line);

    echo "<div class='terminal_text'>";

    $line = explode("==", $line, 4);

    // Commit hash
    echo "<a href='?dir={$_REQUEST["dir"]}&commit={$line[0]}' style='text-decoration:none'>";
    echo "<div style='color:#CCA43D; display:inline-block;'>";
    echo $line[0];
    echo "</div>";
    echo "</a> ";

    // Commit message
    echo "<span style='color:white'>";
    echo $line[3];
    echo "</span> ";

    // Commit date
    echo "<span style='color:#0d9440'>";
    echo $line[1];
    echo "</span> ";

    // Commit author
    echo "<span style='color:#098f96'>";
    echo $line[2];
    echo "</span>";

    echo "</div>";
}
?>


<?php
function handle_commit($commit)
{
    $counter = 0;
    // Make sure commit message doesn't trigger any of the output modifiers below
    $after_diff = false;

    echo "<div class='terminal_text'>";

    foreach($commit as $line)
    {
        // Escape html
        $line = htmlspecialchars($line);

        if ($counter < 4)
        {
            // Commit, author and date
            echo "<span style='color:#CCA43D'>";
            echo $line;
            echo "</span>";
            echo "<br>";
        }
        else if ($counter <= 5)
        {
            // Commit message
            echo "<span style='color:white'>";
            echo $line;
            echo "</span>";
            echo "<br>";
        }
        else if (substr($line, 0, strlen("diff --git")) === "diff --git"
            || substr($line, 0, strlen("diff --cc")) === "diff --cc")
        {
            // File changed
            echo "<hr>";
            echo "<span style='color:#4f8eff;'>";
            echo $line;
            echo "</span>";
            echo "<br>";

            $after_diff = true;
        }
        else if ($after_diff && output_ignore($line)) { }
        else if ($after_diff && substr($line, 0, strlen("new file mode")) === "new file mode")
        {
            // New file label
            echo "<span style='color:#27519c;'>";
            echo $line;
            echo "</span>";
            echo "<br>";
        }
        else if ($after_diff && substr($line, 0, 1) === "+")
        {
            // Added changes
            echo "<span style='color:#00941b'>";
            echo $line;
            echo "</span>";
            echo "<br>";
        }
        else if ($after_diff && substr($line, 0, 1) === "-")
        {
            // Removed changes
            echo "<span style='color:#b80000'>";
            echo $line;
            echo "</span>";
            echo "<br>";
        }
        else if (!$after_diff)
        {
            // Commit message (after title)
            echo "<span style='color:white'>";
            echo $line;
            echo "</span>";
            echo "<br>";
        }
        else
        {
            echo "<span style='color:gray'>";
            echo $line;
            echo "</span>";
            echo "<br>";
        }

        if (!$after_diff) $counter++;
    }

    echo "</div>";
}

function output_ignore($str)
{
    // These are file changes *again*, it's okay to ignore
    if (substr($str, 0, 4) === "+++ " || substr($str, 0, 4) === "--- ")
        return true;

    // Index something line, ignore
    if (substr($str, 0, strlen("index ")) === "index ")
        return true;

    // Ignore line number, doesn't look friendly
    if (substr($str, 0, 3) === "@@ " || substr($str, 0, 4) === "@@@ ")
        return true;
    
    return false;
}
?>
