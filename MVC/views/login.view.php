<?php require "partials/head.php"; ?>

<h2 class="centered">Kirjaudu sisään</h2>

<div class="inputarea">
<form  action="/login" method="post">
    <label for="uname">Käyttäjänimi:</label>
    <input id="uname" type="text" name="username" maxlength=30>
    <label for="pwprd">Salasana:</label>
    <input id="pword" type="password" name="password" maxlength=30>
    <input id="sendbutton" type="submit" value="Lähetä">
</form>
</div>

<?php require "partials/footer.php"; ?>