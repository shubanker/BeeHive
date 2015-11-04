<?PHP 
$con=mysqli_connect("localhost","vikash","kisku","social_test");
//echo ($con)?"connected":"not connected";
$sql="select * from users";
$result=mysqli_query($con,$sql);
while($row=mysqli_fetch_array($result)){
echo $row['user_id']." ".$row['username']."</br>";

}
echo "-----------------------------------------------</br>.UPDATES</br>";
$sql="select * from users,updates where users.user_id=updates.user_id_fk";
$result=mysqli_query($con,$sql);
while($row=mysqli_fetch_array($result)){
echo $row['update_id']." ".$row['username']." ".$row['updated']."</br>";

}
/* for($i=1;$i<4;$i++){
$sql="INSERT INTO friends
(friend_one,friend_two,status)
VALUES
('$i','$i','2');";
$result=mysqli_query($con,$sql);
} */
echo "-----------------------------------------------.Friend Search</br>";

$user_id=4;
$sql="select * from users where user_id!=$user_id";
$result=mysqli_query($con,$sql);
while($row=mysqli_fetch_array($result)){
echo $row['user_id']." ".$row['username']."</br>";

}
echo "-----------------------------------------------.Requests</br>";

 $friend_id=1;
/*
Add Friend
$sql="INSERT INTO friends
(friend_one,friend_two)
VALUES
('$user_id','$friend_id');";
$result=mysqli_query($con,$sql);
 */
$result=mysqli_query($con,"SELECT friend_one,friend_two,status FROM friends WHERE (friend_one=$user_id OR friend_two=$user_id) AND (friend_one=$friend_id OR friend_two=$friend_id)");
while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)){
if($row['friend_one']==$user_id && $row['status']=='0')
{
echo "Friend request sent";
}
	elseif($row['status']=='0'){
 echo "confirm Request";
	}
}


//Confirm Request
$sql="UPDATE friends
SET status='1'
WHERE
(friend_one=$user_id OR friend_two=$user_id)
AND
(friend_one=$friend_id OR friend_two=$friend_id)";
$result=mysqli_query($con,$sql);
 
 echo "--------------------------------------------FRends Status UPdates</br>";
 $sql="SELECT U.username, U.email, D.update_id, D.updated, D.created
FROM users U, updates D, friends F
WHERE
D.user_id_fk = U.user_id
AND

CASE
WHEN F.friend_one = '$user_id'
THEN F.friend_two = D.user_id_fk
WHEN F.friend_two= '$user_id'
THEN F.friend_one= D.user_id_fk
END

AND
F.status > '0'
ORDER BY D.update_id DESC;";
 $result=mysqli_query($con,$sql);
while($row=mysqli_fetch_array($result)){
echo $row['update_id']." ".$row['username']." ".$row['updated']."</br>";
}

echo "-----------------------------------------Friend LIst</br>";
$sql="SELECT F.status, U.username, U.email,U.user_id
FROM users U, friends F
WHERE
CASE

WHEN F.friend_one = '$user_id'
THEN F.friend_two = U.user_id
WHEN F.friend_two= '$user_id'
THEN F.friend_one= U.user_id
END

AND
F.status='1';";
echo "$user_id is friends With</br>";
$result=mysqli_query($con,$sql);
while($row=mysqli_fetch_assoc($result)){
echo $row['user_id']." ".$row['username']."</br>";
}
echo "--------------------------------Mutual Friend";
$sql="SELECT u.user_id,u.username
FROM users u
    LEFT JOIN friends fl
        ON u.user_id = fl.friend_one AND 1 IN (fl.friend_two)
WHERE fl.friend_two IS NULL
AND u.user_id != $friend_id";
$result=mysqli_query($con,$sql);
while($row=mysqli_fetch_assoc($result)){
echo $row['user_id']." ".$row['username']."</br>";
}

 ?>
