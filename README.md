# Stupid Simple Code Reviewer
Small single-file web browser for git written in PHP.

## Features
- Browse commits<br>
  List all commits present in source branch, but not in target branch (defaults to master).<br>
  Make sure to pull all commits in both branches.<br>
  Make sure both branches exist locally.<br>
  Make sure to specify a valid directory for git dir.<br>
  <br>
  You can **click on a commit hash** to view commit diff.
  
- See file diff<br>
  Show file changes for commit.<br>
  Make sure commit is pulled locally.<br>

### Browse by URL
You can browse commits and view diffs directly by entering url parameters manually.

Example of browsing commits present in branch *feature-new-branch*, but not present in default branch:
```
http://localhost/gitbrowser.php?dir=/home/luka/repo&branch_1=feature-new-branch
```

Example of diff present in commit *abcdef012*:
```
http://localhost/gitbrowser.php?dir=/home/luka/repo&commit=abcdef012
```

## Installation
Place **gitwrapper.php** anywhere with a configured PHP web server and access to a desired git repository.

## TO-DO/WIP
- Merge commits aren't always shown properly
- **No input sanitization**
- No validation or error handling
