<?php
    session_start();
    include 'db.php';

    if(isset($_REQUEST["q"])){
        $q = $_REQUEST["q"];

        if (!($q === '')){
            $sql = $conn->prepare( "SELECT pic_file
	    FROM items where locate(?, name) > 0 order by locate(?, name)");
	    $sql->bind_param("ss", $que, $que);
	    $que = $q;
	    $sql->execute();
            $result = $sql->get_result();
            $record = $result->fetch_all();
    
            $arr = array();
    
            foreach($record as $row){
                $arr[] = "$row[0]";
            }
    
            $sql = $conn->prepare("SELECT pic_file
	    FROM items where locate(?, name) = 0");
	    $sql->bind_param("s", $que);
	    $que = $q;
	    $sql->execute(); 
            $result = $sql->get_result();
            $record = $result->fetch_all();
    
            foreach($record as $row){
                $arr[] = "$row[0]";
            }
    
            print_r(json_encode($arr));
            unset($q);
            return;
            
        }
    }

    if(isset($_REQUEST["pic_file"])){
        $file_req = $_REQUEST["pic_file"];
        if(!($file_req === '')){
            $sql = "SELECT name
            FROM items where pic_file = '$file_req';";
            $result = $conn->query($sql);
            $record = $result->fetch_row();
            print_r(json_encode($record));
            unset($file_req);
            return;
        }
    }


    if(isset($_REQUEST["pic_file_add"])){
        $file_req = $_REQUEST["pic_file_add"];
        if(!($file_req === '')){
            $sql = "SELECT items.name, id FROM items where pic_file = '$file_req';";
            $result = $conn->query($sql);
            $record = $result->fetch_row();

            $arr = array();
            $arr[] = $record[0];
            $arr[] = $record[1];

            $sql = "set @exp_days = (select fridge_days from items where id='$record[1]');";
            $result = $conn->query($sql);

            $sql = "select @exp_days;";
            $result = $conn->query($sql);

            $sql = "set @exp_date = (select DATE_ADD(current_date(), INTERVAL @exp_days DAY));";
            $result = $conn->query($sql);

            $sql = "select @exp_date;";
            $result = $conn->query($sql);

            $sql = "select @exp_date;";
            $result = $conn->query($sql);
            $record = $result->fetch_row();
            $arr[] = $record[0];

            print_r(json_encode($arr));
            unset($file_req);
            return;
        }
    }

    if(isset($_REQUEST["id"])){
        $item_id = $_REQUEST["id"];
        $log_id = $_REQUEST["log_id"];
        $exp_date = $_REQUEST["exp_date"];
        $username = $_SESSION["username"];
        $quantity = $_REQUEST["quantity"];
        $man_date = $_REQUEST["man_date"];
        $freshness = $_REQUEST["freshness"];

        if($log_id == ''){   // atp we know user is tryna add item NOT update
            if(($quantity != '') && ($man_date != '')){
                $sql = $conn->prepare("insert into user_items (username, item_id, quantity, manufacture_date, expiry_date, log_date, freshness, mod_date) 
		values(?, '$item_id', '$quantity', '$man_date', '$exp_date', current_date(), '$freshness', current_date())");
            }
            else if(($quantity == '') && ($man_date != '')){
                $sql = $conn->prepare("insert into user_items (username, item_id, quantity, manufacture_date, expiry_date, log_date, freshness, mod_date) 
                values(?, '$item_id', null, '$man_date', '$exp_date', current_date(), '$freshness', current_date())");
            }
            else if (($quantity != '') && ($man_date == '')){
                $sql = $conn->prepare("insert into user_items (username, item_id, quantity, manufacture_date, expiry_date, log_date, freshness, mod_date) 
                values(?, '$item_id', '$quantity', null, '$exp_date', current_date(), '$freshness', current_date())");
            }
            else{
                $sql = $conn->prepare( "insert into user_items (username, item_id, quantity, manufacture_date, expiry_date, log_date, freshness, mod_date) 
                values(?, '$item_id', null, null, '$exp_date', current_date(), '$freshness', current_date())");
	    }
	    $sql->bind_param("s", $uname);
	    $uname = $username;
	    $result = $sql->execute();
            print_r(json_encode([true]));
        }
        else{
            // $sql = "select user_items.expiry_date from user_items where log_id=$log_id;";
            // $result = $conn->query($sql);
            // $records = ($result->fetch_row())[0];

            // if($records != $exp_date){
            //     $sql = "update user_items set log_date=current_date() where log_id=$log_id;";
            //     $result = $conn->query($sql);
            // }

            $sql = "select user_items.quantity, user_items.freshness from user_items where log_id=$log_id;";
            $result = $conn->query($sql);
            $record1 = ($result->fetch_row())[0];

            if(($record1 == 0) && ($quantity != 0)){
                $sql = "update user_items set log_date=current_date(), freshness=$freshness, mod_date=current_date() where log_id=$log_id;";
                $result = $conn->query($sql);
            }

            $sql = "select user_items.freshness from user_items where log_id=$log_id;";
            $result = $conn->query($sql);
            $record1 = ($result->fetch_row())[0];

            if(($record1 != $freshness)){
                $sql = "update user_items set freshness=$freshness, mod_date=current_date() where log_id=$log_id;";
                $result = $conn->query($sql);
            }

            if(($quantity != '') && ($man_date != '')){
                $sql = "update user_items set quantity=$quantity, manufacture_date='$man_date', expiry_date='$exp_date' where log_id=$log_id;";
            }
            else if(($quantity == '') && ($man_date != '')){
                $sql = "update user_items set quantity=null, manufacture_date='$man_date', expiry_date='$exp_date' where log_id=$log_id;";
            }
            else if (($quantity != '') && ($man_date == '')){
                $sql = "update user_items set quantity=$quantity, manufacture_date=null, expiry_date='$exp_date' where log_id=$log_id;";
            }
            else{
                $sql = "update user_items set quantity=null, manufacture_date=null, expiry_date='$exp_date' where log_id=$log_id;";
            }

            $result = $conn->query($sql);
            print_r(json_encode([$result]));
        }
        unset($item_id, $log_id, $exp_date, $quantity, $man_date, $freshness);
        return;
        
    }

    if(isset($_REQUEST["getUserItems"])){
        $username = $_SESSION["username"];
        $sort_selection = $_REQUEST["sort_by"];

        // $sql = "update user_items join items on user_items.item_id = items.id set user_items.freshness = 
        //     greatest(0, user_items.freshness-(DATEDIFF(current_date(), user_items.log_date)/(items.fridge_days/10))) 
        //     where user_items.log_id>=0 and user_items.username='$username';";
        // $conn->query($sql);

        switch($sort_selection){
            case 1:{
                $sql = $conn->prepare( "select items.pic_file, user_items.log_id, user_items.expiry_date, user_items.quantity
                from user_items inner join items on items.id = user_items.item_id and user_items.username=? order by user_items.log_date DESC, user_items.log_id DESC");
                break;
            }
            case 2:{
                $sql = $conn->prepare("select items.pic_file, user_items.log_id, user_items.expiry_date, user_items.quantity from user_items inner join items 
                on items.id = user_items.item_id and user_items.username=? order by user_items.log_date ASC");
                break;
            }
            case 3:{
                $sql = $conn->prepare("select items.pic_file, user_items.log_id, user_items.expiry_date, user_items.quantity, items.name from user_items inner join items 
                on items.id = user_items.item_id and user_items.username=? order by user_items.expiry_date DESC, user_items.log_id DESC");
                break;
            }
            case 4:{
                $sql = $conn->prepare("select items.pic_file, user_items.log_id, user_items.expiry_date, user_items.quantity from user_items inner join items 
                on items.id = user_items.item_id and user_items.username=? order by user_items.expiry_date ASC");
                break;
            }
            case 5:{
                $sql = $conn->prepare("select items.pic_file, user_items.log_id, user_items.expiry_date, user_items.quantity from user_items inner join items 
                on items.id = user_items.item_id and user_items.username=? 
                where user_items.quantity is null or user_items.quantity > 0 order by user_items.quantity DESC");
		$sql->bind_param("s", $uname);
		$uname = $username;
		$sql->execute();
		$result = $sql->get_result();
                $records1 = $result->fetch_all();
                $sql = $conn->prepare("select items.pic_file, user_items.log_id, user_items.expiry_date, user_items.quantity from user_items inner join items 
                on items.id = user_items.item_id and user_items.username=? 
                where user_items.quantity is not null and user_items.quantity = 0 order by user_items.quantity DESC");
		$i = 0;
		$sql->bind_param("s", $uname);
                $uname = $username;
                $sql->execute();
                $result = $sql->get_result();
                $temp = $result->fetch_row();
                $num = count($records1);
                while($temp != null){
                    $records1[$num+$i] = $temp;
                    $temp = $result->fetch_row();
                    $i++;
                }
                print_r(json_encode([$records1]));
                unset($username, $sort_selection);
                return;
                break;
            }
            case 6:{
                $sql = $conn->prepare("select items.pic_file, user_items.log_id, user_items.expiry_date, user_items.quantity from user_items inner join items 
                on items.id = user_items.item_id and user_items.username=? 
                where user_items.quantity is not null and user_items.quantity = 0 order by user_items.quantity DESC");
                $sql->bind_param("s", $uname);
                $uname = $username;
                $sql->execute();
                $result = $sql->get_result();
		$records1 = $result->fetch_all();

                $sql = $conn->prepare("select items.pic_file, user_items.log_id, user_items.expiry_date, user_items.quantity from user_items inner join items 
                on items.id = user_items.item_id and user_items.username=? 
                where user_items.quantity is null or user_items.quantity > 0 order by user_items.quantity ASC");
		$i = 0;
		$sql->bind_param("s", $uname);
                $uname = $username;
                $sql->execute();
                $result = $sql->get_result();
                $temp = $result->fetch_row();
                $num = count($records1);
                while($temp != null){
                    $records1[$num+$i] = $temp;
                    $temp = $result->fetch_row();
                    $i++;
                }
                print_r(json_encode([$records1]));
                unset($username, $sort_selection);
                return;
                break;
            }
            case "3b":{
                $sql = $conn->prepare("select sum(quantity), name from user_items join items on items.id = user_items.item_id and user_items.username = ?
                where DATEDIFF(user_items.expiry_date, current_date()) >= 0 group by name");  // this is for recipe inspo section, see function getRecipes() in fridge.js
                break;
            }
        }
        $sql->bind_param("s", $uname);
        $uname = $username;
        $sql->execute();
        $result = $sql->get_result();
        $records = $result->fetch_all();
        print_r(json_encode([$records]));
        unset($username, $sort_selection);
        return;
    }

    if(isset($_REQUEST["getLog"])){
        $log_id = $_REQUEST["getLog"];
        $sql = "select items.name, user_items.quantity, user_items.manufacture_date, user_items.expiry_date, user_items.log_date, 
        greatest(0, user_items.freshness-(DATEDIFF(current_date(), user_items.mod_date)/(items.fridge_days/10))) from user_items inner join items on items.id = user_items.item_id and log_id='$log_id';";
        $result = $conn->query($sql);
        $records = $result->fetch_row();
        print_r(json_encode([$records]));      
    

        unset($log_id);
        return;
    }

    if(isset($_REQUEST["confirm_remove_id"])){
        $confirm_remove_id = $_REQUEST["confirm_remove_id"];   // this is the log id to remove
        $sql = "select items.name from items inner join user_items on items.id = user_items.item_id and log_id='$confirm_remove_id';";
        $result = $conn->query($sql);
        $record = $result->fetch_row();
        print_r(json_encode([$record]));        
        unset($confirm_remove_id);
        return;
    }

    if(isset($_REQUEST["remove_id"])){
	$remove_id = $_REQUEST["remove_id"];   // this is the log id to remove
	$sql = "select log_id, username, item_id, expiry_date, log_date  from user_items where log_id='$remove_id'";
	$result = $conn->query($sql);
	$record = $result->fetch_row();
	$id = $record[0];
	$item_id = $record[2];
	$exp = $record[3];
	$log = $record[4];
	$sql = $conn->prepare("insert into user_history (log_id, username, item_id, expiry_date, log_date, date_removed) values ('$id', ?,'$item_id', '$exp', '$log', current_date());");
	$sql->bind_param("s", $uname);
	$uname = $record[1];
	$sql->execute();

        $sql = "delete from user_items where log_id='$remove_id'";
	$result = $conn->query($sql);
        print_r(json_encode([$result]));        
        unset($remove_id);
        return;
    }

    if(isset($_REQUEST["sort-options"])){
        $sort_selection = $_REQUEST["sort-options"];  
        $username = $_SESSION["username"];
	$sql = $conn->prepare("update users set sort_selection='$sort_selection' where username=?");
	$sql->bind_param("s", $uname);
	$uname = $username;
	$sql->execute();
        print_r(json_encode([1,2,1]));        
        unset($sort_selection, $username);
        return;
    }


    if(isset($_REQUEST["getSortMethod"])){
        $username = $_SESSION["username"];
        $sql = $conn->prepare("select sort_selection from users where username=?");
        $sql->bind_param("s", $uname);
        $uname = $username;
        $sql->execute();
        $result = $sql->get_result();
        $records = $result->fetch_row();
        print_r(json_encode([$records]));
        unset($username);
        return;
    }

    if(isset($_REQUEST["reportCode"])){
        $username = $_SESSION["username"];
        $reportCode = $_REQUEST["reportCode"];
        switch($reportCode){
            case "collapse1":{
                $sql = $conn->prepare("select items.name, user_items.log_id, DATEDIFF(user_items.expiry_date, current_date()) from items join user_items on items.id=user_items.item_id 
                where (DATEDIFF(user_items.expiry_date, current_date()) >= 0 and DATEDIFF(user_items.expiry_date, current_date()) < 3) and user_items.username=? 
                and (user_items.quantity is null or user_items.quantity > 0) order by items.name");
                $sql->bind_param("s", $uname);
                $uname = $username;
                $sql->execute();
                $result = $sql->get_result();
                $records = $result->fetch_all();
                break;
            }
            case "collapse2":{
                $sql = $conn->prepare("select items.name, user_items.log_id, DATEDIFF(current_date(), user_items.expiry_date) from items join user_items on items.id=user_items.item_id 
		where user_items.expiry_date < current_date() and user_items.username=? and (user_items.quantity is null or user_items.quantity > 0) order by items.name");
                $sql->bind_param("s", $uname);
                $uname = $username;
                $sql->execute();
                $result = $sql->get_result();
                $records = $result->fetch_all();
                break;

            }
            // case "collapse3":{
            //     break;
            // }
            case "collapse4":{
                $sql = $conn->prepare("select items.name, user_items.log_id from items join user_items on items.id=user_items.item_id 
                where user_items.log_date = current_date() and user_items.username=? order by items.name");
                $sql->bind_param("s", $uname);
                $uname = $username;
                $sql->execute();
                $result = $sql->get_result();
                $records = $result->fetch_all();
                break;

            }
            case "collapse5":{
                $sql = $conn->prepare("select items.name, user_items.log_id from items join user_items on items.id=user_items.item_id 
                where user_items.quantity=0 and user_items.username=? order by items.name");
                $sql->bind_param("s", $uname);
                $uname = $username;
                $sql->execute();
                $result = $sql->get_result();
                $records = $result->fetch_all();
                break;
            }
            // case "collapse6":{
            //     $sql = "select items.name, user_items.log_id from items join user_items on items.id=user_items.item_id 
            //     where not user_items.quantity=0 and user_items.username='$username' and DATEDIFF(user_items.expiry_date, user_items.log_date) > 0
            //     and DATEDIFF(user_items.log_date, user_items.expiry_date) order by items.name;";
            //     $result = $conn->query($sql);
            //     $records = $result->fetch_all();
            //     break;

            // }
        }
        print_r(json_encode([$records]));
        unset($username, $reportCode);
        return;
    }

    if(isset($_REQUEST["exp-clr"]) || isset($_REQUEST["out-clr"]) || isset($_REQUEST["all-clr"])){
        $username = $_SESSION["username"];

	if(isset($_REQUEST["all-clr"])){
		$sql = $conn->prepare("select log_id, username, item_id, expiry_date, log_date from user_items where username=?");
		$sql->bind_param("s", $uname);
		$uname = $username;
		$sql->execute();
		$result = $sql->get_result();
		while($record = $result->fetch_row()){
                        $id = $record[0];
                        $item_id = $record[2];
                        $exp = $record[3];
                        $log = $record[4];
                        $sql = $conn->prepare("insert into user_history (log_id, username, item_id, expiry_date, log_date, date_removed) values ('$id', ?,'$item_id', '$exp', '$log', current_date());");
                        $sql->bind_param("s", $uname);
                        $uname = $record[1];
                        $sql->execute();
		}

            $sql = $conn->prepare("delete from user_items where user_items.username=?");
            $sql->bind_param("s", $uname);
            $uname = $username;
            $sql->execute();
            $result = $sql->get_result();    
            return;
        }

	if(isset($_REQUEST["out-clr"])){
                $sql = $conn->prepare("select log_id, username, item_id, expiry_date, log_date from user_items where username=? and quantity=0");
		$sql->bind_param("s", $uname);
		$uname = $username;
		$sql->execute();

                $result = $sql->get_result();
                while($record = $result->fetch_row()){
                        $id = $record[0];
                        $item_id = $record[2];
                        $exp = $record[3];
                        $log = $record[4];
                        $sql = $conn->prepare("insert into user_history (log_id, username, item_id, expiry_date, log_date, date_removed) values ('$id', ?,'$item_id', '$exp', '$log', current_date());");
                        $sql->bind_param("s", $uname);
                        $uname = $record[1];
                        $sql->execute();
                }
                
            $sql = $conn->prepare("delete from user_items where user_items.username=? and user_items.quantity=0");
            $sql->bind_param("s", $uname);
            $uname = $username;
            $sql->execute();
            $result = $sql->get_result();
        }

        if(isset($_REQUEST["exp-clr"])){
                $sql = $conn->prepare("select log_id, username, item_id, expiry_date, log_date from user_items where username=? and DATEDIFF(user_items.expiry_date, current_date()) < 0");
                $sql->bind_param("s", $uname);
                $uname = $username;
                $sql->execute();

                $result = $sql->get_result();
                while($record = $result->fetch_row()){
                        $id = $record[0];
                        $item_id = $record[2];
                        $exp = $record[3];
                        $log = $record[4];
                        $sql = $conn->prepare("insert into user_history (log_id, username, item_id, expiry_date, log_date, date_removed) values ('$id', ?,'$item_id', '$exp', '$log', current_date());");
                        $sql->bind_param("s", $uname);
                        $uname = $record[1];
                        $sql->execute();
                }		
	    $sql = $conn->prepare("delete from user_items where user_items.username=? and DATEDIFF(user_items.expiry_date, current_date()) < 0");
            $sql->bind_param("s", $uname);
            $uname = $username;
            $sql->execute();
            $result = $sql->get_result();
        }  
    
        unset($username);
        return;
    }

    if(isset($_REQUEST["itemId"])){
        $itemId = $_REQUEST["itemId"];
        $sql = "select items.fridge_days from items where items.id='$itemId'";
        $result = $conn->query($sql);
        $records = $result->fetch_row();
        print_r(json_encode([$records]));
        unset($itemId);
        return;
    }

    if(isset($_REQUEST["metrics"])){
	    $code = $_REQUEST["metrics"];
	    $username = $_SESSION["username"];
	switch($code){
		case 1:{
			$sql = $conn->prepare("select t1.m, sum(t1.c) from (select month(expiry_date) as m, count(*) as c from user_history where username=? and DATEDIFF(date_removed, expiry_date) > 0 and expiry_date between concat(year(CURRENT_DATE),'-01-01') and concat(year(CURRENT_DATE),'-12-31') group by month(expiry_date) UNION all select month(expiry_date) as m, count(*) as c from user_items where username=? and DATEDIFF(CURRENT_DATE, expiry_date) > 0 and expiry_date between concat(year(CURRENT_DATE),'-01-01') and concat(year(CURRENT_DATE),'-12-31') group by month(expiry_date)) as t1 GROUP by t1.m");
			break;
		}
		case 2:{
			$sql = $conn->prepare("select t1.m, sum(t1.c) as q from (select month(date_removed) as m, count(*) as c from user_history where username=? and DATEDIFF(date_removed, expiry_date) <= 0 and date_removed between concat(year(CURRENT_DATE),'-01-01') and concat(year(CURRENT_DATE),'-12-31') group by month(date_removed) UNION all select month(log_date) as m, count(*) as c from user_items where username=? and DATEDIFF(log_date, expiry_date) <= 0 and quantity=0 and log_date between concat(year(CURRENT_DATE),'-01-01') and concat(year(CURRENT_DATE),'-12-31') group by month(log_date)) as t1 group by t1.m");
			break;
		}
		case 3:{
			$sql = $conn->prepare("select t1.name, sum(t1.c) as q from (select name, count(*) as c from items inner join user_items on id=item_id where username=? and log_date between concat(year(CURRENT_DATE),'-01-01') and concat(year(CURRENT_DATE),'-12-31') group by id UNION all select name, count(*) as C from items inner join user_history on id=item_id where username=? and log_date between concat(year(CURRENT_DATE),'-01-01') and concat(year(CURRENT_DATE),'-12-31') group by id) as t1 group by t1.name order by q desc");
			break;
		}
		case 4:{
			$sql = $conn->prepare("select t1.name, sum(t1.c) as q from (select name, count(*) as c from items inner join user_items on id=item_id where username=? and expiry_date between concat(year(CURRENT_DATE),'-01-01') and concat(year(CURRENT_DATE),'-12-31') and datediff(CURRENT_DATE, expiry_date) > 0 group by id 
UNION all 
select name, count(*) as c from items inner join user_history on id=item_id where username=? and expiry_date between concat(year(CURRENT_DATE),'-01-01') and concat(year(CURRENT_DATE),'-12-31') and datediff(date_removed, expiry_date) > 0 group by id) as t1 group by t1.name order by q desc");
			break;
		}
	}
	$sql->bind_param("ss", $uname, $uname);
	$uname = $username;
	$sql->execute();
	$result = $sql->get_result();
	$records = $result->fetch_all();
        print_r(json_encode($records));
        unset($username, $code);
        return;
    }
    
?>
