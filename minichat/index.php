<?php
    try
    {
        $bdd = new PDO('mysql:host=127.0.0.1;dbname=minichat;charset=utf8',
                       'root',
                       '',
                       array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }
    catch (Exception $e)
    {
        die('Erreur : ' . $e->getMessage());
    }
   
    if(isset($_POST['nickname']) and isset($_POST['message'])){
        // store cookie
        $nickname=$_POST['nickname'];
        $currentnickname=$nickname;
        setcookie('nickname',$nickname,time()+365*24*3600,null,null,false,true);
        // echo($_COOKIE['nickname']);exit;
        // lier l'id au nickname user id lien avec id de la table users
        $userstatement=$bdd->prepare('SELECT * FROM users WHERE nickname=?');
        $userstatement->execute([$_POST['nickname']]);
        $user=$userstatement->fetch(PDO :: FETCH_ASSOC);
        if($user){
            $user_id=$user['id'];
        }
        else{
            // si user inconnu , le rajouter à la table users
            $nickname=$_POST['nickname'];
            date_default_timezone_set('Europe/Paris');
            $createdat=date('Y-m-d H:i:s');
            $ipadress=$_SERVER['REMOTE_ADDR'];
                    
            $requestuserinsert=$bdd->PREPARE('INSERT INTO users (nickname,created_at,ip_address) VALUES(?,?,?)');
            $requestuserinsert->EXECUTE([
                $nickname,
                $createdat,
                $ipadress
            ]);
            // et récupérer son id 
            $user_id=$bdd->lastInsertId();
        }
        //  pour enfin push le message dans la table messages
        date_default_timezone_set('Europe/Paris');
        $createdatmess=date('Y-m-d H:i:s');
        $ipadressmess=$_SERVER['REMOTE_ADDR'];
        $color='null';
        $message=$_POST['message'];

        $request_insert_message=$bdd->PREPARE('INSERT INTO messages (user_id,message,ip_address,color,created_at) 
        VALUES(?,?,?,?,?)');
        $request_insert_message->EXECUTE([
                    $user_id,
                    $message,
                    $ipadressmess,
                    $color,
                    $createdatmess
                ]);
             
    }
    // recup tous les messages et les afficher dans le container message
    $all_messages_prep=$bdd->query('SELECT messages.*, users.nickname FROM users INNER JOIN messages WHERE users.id=messages.user_id');
    $allMessages=$all_messages_prep->fetchAll(PDO :: FETCH_ASSOC);    
    // echo '<pre>';
    // print_r($allMessages);exit;
    // recup tous les users et les mettre dans le container users
    $all_users_request=$bdd->query('SELECT users.nickname FROM users');
    $all_users=$all_users_request->fetchAll(PDO::FETCH_ASSOC);
    // echo '<pre>';
    // print_r($all_users);exit;

    

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" 
    integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
    <title>Document</title>
</head>
<body>
<h1>Mini Chat</h1>
   
<div class="container">
    <form action="index.php" method="POST">
        <div class="global">
            <div class="form-group col-md-3 mb-2">
                    <label for="connected_users">connected users</label>
                    <textarea class="form-control" id="connected_users" col="4" rows="15">
                    <?php foreach($all_users as $user):?>
                          <?php echo($user['nickname'])?>
 
                    <?php endforeach;?>
                         
                                       
                    </textarea>
            </div>
            
            <div class="form-group col-md-9 mb-8">
                <label for="exampleFormControlTextarea1">Messages</label>
                <textarea class="form-control" id="messages" rows="15">
                
                <?php foreach($allMessages as $message):?>
                
                <?php echo($message['nickname'])?><?="          "?> <?=$message['message'] ?>
                <?php endforeach;?>
                </textarea>
            </div>
        </div>
        
        <div > 
            
            <label for="nickname">nickname</label>
            <input type="text" name ="nickname" value="<?php echo($_COOKIE['nickname'])?>"><br>
            
            
            <label for="message"> new message</label>
            <textarea class="form-control" name ="message" id="new message" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    
    
</div>

   
<script>

</script>


</body>
</html>