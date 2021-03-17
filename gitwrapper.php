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

<!-- 
    main screen
    - starting point of app
    - used to input directory of git repo (not counting the /.git part)
-->
<? if ($screen == "main") { ?>
    <div id="main-content">
        <div class="main-screen">
            <p class="title"> GIT Browser </p>
            <p style="text-align:left;margin-left:20px;">Git directory:</p>
            <div>
                <span class = "col-md-11">
                    <input id="git_dir" class="form-control" style="min-height:50px;text-align:right;font-size:20px;" type="text" placeholder="git repo directory">
                </span>
                <span class = "col-md-1" style="margin-left:-20px;font-size:20px;">/.git</span>
            <button class="btn btn-default btn btn-primary" style="font-size:20px;margin-top:20px;"
                onclick="to_branch_pick();">Next</button>
        </div>
    </div>
<? } ?>


<!-- 
    branch pick
    - after main
    - used to input branches to be compared, target branch is master by default
-->
<? if ($screen == "branch_pick") { ?>
    <div id="main-content">
        <div class="main-screen">
            <p class="title"> Compare branch to target </p>
            <p style="text-align:left;margin-left:5px;margin-top:-10px;">Git branch:</p>
            <input id="branch_1" class="form-control" style="min-height:50px;font-size:20px;" type="text" value="" placeholder="branch-name">
            <button class="btn btn-default btn" style="font-size:25px;margin-top:15px;"
                title="switch branches" onclick="branch_switch();">⭥</button>
            <p style="text-align:left;margin-left:5px;margin-top:0px;">Target branch (e.g. master):</p>
            <input id="branch_2" class="form-control" style="min-height:50px;font-size:20px;" type="text" value="master" placeholder="master">
            <button class="btn btn-default btn" style="font-size:20px;margin-top:20px;"
                onclick="to_main();">Back</button>
            <button class="btn btn-default btn btn-primary" style="font-size:20px;margin-top:20px;"
                onclick="to_commit_list('<?= $_REQUEST["dir"] ?>');">Next</button>
        </div>
    </div>
<? } ?>


<!-- 
    commit list
    - after branch pick
    - list all found commits, commit can be clicked to show commit diff screen
-->
<? if ($screen == "commit_list") { ?>
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
<? } ?>


<!-- 
    commit diff
    - after commit list
    - show diff for certain commit
-->
<? if ($screen == "commit_diff") { ?>
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
<? } ?>

<?php
?>
<?php
    $after_diff = false;
            $after_diff = true;
        else if ($after_diff && output_ignore($line)) { }
        else if ($after_diff && substr($line, 0, strlen("new file mode")) === "new file mode")
        else if ($after_diff && substr($line, 0, 1) === "+")
        else if ($after_diff && substr($line, 0, 1) === "-")
        else if (!$after_diff)
        if (!$after_diff) $counter++;
    // These are file changes *again*, it's okay to ignore