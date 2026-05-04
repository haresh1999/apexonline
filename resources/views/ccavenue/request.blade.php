<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CC Avenue | Redirect</title>
</head>

<body>
    <form method="post" name="redirect" action="{{ $postUrl }}?command=initiateTransaction">
        <input type="hidden" name="encRequest" value="{{ $encryptedData }}">
        <input type="hidden" name="access_code" value="{{ $accessCode }}">
    </form>
    <script language='javascript'>
        document.redirect.submit();
    </script>
</body>

</html>