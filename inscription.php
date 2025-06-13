<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Gestion de Stock</title>
    
</head>
<body>
    <div class="container">
        <div class="form form--signup">
            <div class="form--heading">Inscription</div>
            <form id="register-form" action="traitement_inscription.php" method="post">
                <input type="text" placeholder="Nom d'utilisateur" id="username" name="username" required>
                <input type="email" placeholder="Email" id="email" name="email" required>
                <input type="number" placeholder="Âge" id="age" name="age" required>
                <input type="password" placeholder="Mot de passe" id="password" name="password" required>
                <label for="role">Type d'utilisateur:</label>
                <select id="role" name="role" required>
                    <option value="admin">Administrateur</option>
                    <option value="supplier">Fournisseur</option>
                    <option value="client">Client</option>
                </select>
                <button class="button" type="submit">S'inscrire</button>
            </form>
        </div>
        <p>Déjà un compte? <a href="index.php">Connectez-vous ici</a>.</p>
    </div>
</body>
</html>
<script>
    $("#signup").click(function() {
    $(".message").css("transform", "translateX(100%)");
    if ($(".message").hasClass("login")) {
        $(".message").removeClass("login");
    }
    $(".message").addClass("signup");
});

$("#login").click(function() {
    $(".message").css("transform", "translateX(0)");
    if ($(".message").hasClass("signup")) {
        $(".message").removeClass("signup");
    }
    $(".message").addClass("login");
});
</script>
<style>
    @import url("https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400");
@import url("https://fonts.googleapis.com/css?family=Playfair+Display");

body,
.message,
.form,
form {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

body {
    height: 100vh;
    background: #e8e8e8;
    font-family: 'Source Sans Pro', sans-serif;
    overflow: hidden;
}

.container {
    width: 700px;
    height: 400px;
    background: white;
    position: relative;
    display: grid;
    grid-template: 100% / 50% 50%;
    box-shadow: 2px 2px 10px 0 rgba(51, 51, 51, 0.2);
}

.message {
    position: absolute;
    background: white;
    width: 50%;
    height: 100%;
    transition: 0.5s all ease;
    transform: translateX(100%);
    z-index: 4;
}

.message:before {
    position: absolute;
    content: "";
    width: 1px;
    height: 70%;
    background: #c3c3d8;
    opacity: 0;
    left: 0;
    top: 15%;
}

.signup:before {
    opacity: 0.3;
    left: 0;
}

.login:before {
    opacity: 0.3;
    left: 100%;
}

.btn-wrapper {
    width: 60%;
}

.button {
    margin: 5px 0;
    width: 100%;
    height: 30px;
    border: 0;
    outline: 0;
    color: white;
    font-size: 15px;
    font-weight: 400;
    position: relative;
    z-index: 3;
    background: #129D72;
    font-family: 'Source Sans Pro', sans-serif;
    cursor: pointer;
}

.button:hover {
    background-color: #0a7b57;
}

.form {
    width: 100%;
    height: 100%;
}

.form--heading {
    font-size: 25px;
    height: 50px;
    color: #129D72;
}

.form--signup {
    border-right: 1px solid #999;
}

form {
    width: 70%;
}

form > * {
    margin: 10px;
}

input {
    width: 90%;
    border: 0;
    border-bottom: 1px solid #aaa;
    font-size: 13px;
    font-weight: 300;
    color: #797a9e;
    letter-spacing: 0.11em;
}

input::placeholder {
    color: #333;
    font-size: 10px;
}

input:focus {
    outline: 0;
    border-bottom: 1px solid rgba(128, 155, 206, 0.7);
    transition: 0.6s all ease;
}

label {
    display: block;
    margin-top: 15px;
    text-align: left;
    color: #555;
}

select {
    width: 90%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 16px;
}

p {
    margin-top: 20px;
    color: #555;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
    </style>