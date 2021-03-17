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