<?php
    include("db_connect.php");
    $request_method = $_SERVER["REQUEST_METHOD"];

    function getContacts()
    {
        global $conn;
        $query = "SELECT * FROM contacts";
        $response = array();
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $response[] = $row;
        }
        //header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    function getContact($id=0)
    {
        global $conn;
        $query = "SELECT * FROM contacts";
        if($id != 0)
        {
            $query .= " WHERE id=".$id." LIMIT 1";
        }
        $response = array();
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $response[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }


    function AddContact()
    {
        global $conn;

        $name = $_REQUEST["name"];
        $phone = $_REQUEST["phone"];
        $city = $_REQUEST["city"];
        $mail = $_REQUEST["mail"];
        $website = $_REQUEST["website"];
        $created = date('Y-m-d H:i:s');
        echo $query = "INSERT INTO contacts(name, phone, city, mail, website, created) VALUES('" . $name . "', '" . $phone . "', '" . $city . "' , '" . $mail . "', '" . $website . "', '" . $created . "')";
        if (mysqli_query($conn, $query)) {
            $response = array(
                'status' => 1,
                'status_message' => 'contact ajoute avec succes.'
            );
        } else {
            $response = array(
                'status' => 0,
                'status_message' => 'ERREUR!.' . mysqli_error($conn)
            );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function updateContact($id)
    {
        global $conn;
        parse_str(file_get_contents('php://input'), $_PUT);
        $name = $_REQUEST["name"];
        $phone = $_REQUEST["phone"];
        $city = $_REQUEST["city"];
        $mail = $_REQUEST["mail"];
        $website = $_REQUEST["website"];
        $created = date('Y-m-d H:i:s');

        $query="UPDATE contacts SET name='".$name."', phone='".$phone."', city='".$city."', mail='".$mail."', website='".$website."', created='".$created."' WHERE id=".$id;

        if(mysqli_query($conn, $query))
        {
            $response=array(
                'status' => 1,
                'status_message' =>'Produit mis a jour avec succes.'
            );
        }
        else
        {
            $response=array(
                'status' => 0,
                'status_message' =>'Echec de la mise a jour de contact. '. mysqli_error($conn)
            );

        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function DeleteContact($id)
    {
        global $conn;
        $query = "DELETE FROM contacts WHERE id=".$id;
        if(mysqli_query($conn, $query))
        {
            $response=array(
                'status' => 1,
                'status_message' =>'Produit supprime avec succes.'
            );
        }
        else
        {
            $response=array(
                'status' => 0,
                'status_message' =>'La suppression du produit a echoue. '. mysqli_error($conn)
            );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    switch($request_method)
        {
            case 'GET':
                if(!empty($_REQUEST["id"]))
                {
                    $id = intval($_REQUEST["id"]);
                    getContact($id);
                }
                else
                {
                    getContacts();
                }
                break;
            case 'POST':
                AddContact();
                break;
            case 'PUT':
                // Modifier un produit
                $id = intval($_REQUEST["id"]);
                updateContact($id);
                break;
        case 'DELETE':
            $id = intval($_REQUEST["id"]);
            DeleteContact($id);
            break;
            default:
                header("HTTP/1.0 405 Method Not Allowed");
                break;
    }