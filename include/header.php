<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chess.com Club Manager</title>
    <link rel="stylesheet" href="include/styles.css">
    <script src="include/sorttable.js"></script>
</head>
<script>
function ShowMsg(textVal)
{
document.getElementById("msgid").innerHTML=textVal
}

function ClearMsg()
{
document.getElementById("msgid").innerHTML=""
}

function CopyToClipboard(containerid) {
        if (document.selection) {
            var range = document.body.createTextRange();
            range.moveToElementText(document.getElementById(containerid));
            range.select();
        } else if (window.getSelection) {
            var range = document.createRange();
            range.selectNode(document.getElementById(containerid));
            window.getSelection().empty();
            window.getSelection().addRange(range);
        }
        document.execCommand("Copy");
}

</script>
 