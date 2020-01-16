<?php
    session_start();
    //session_destroy();

    function readCSV($csvFile){
        $file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle) ) {
            $line_of_text[] = fgetcsv($file_handle, 1024);
        }
        fclose($file_handle);
        return $line_of_text;
    }

    function alert($message){
        echo "<script type='text/javascript'>alert('$message')</script>";
    }



    echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\">
          Select csv file to upload:
          <input type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\">
          <input type=\"submit\" value=\"Upload CSV\" name=\"submitCsv\">
          </form><br><br>";

    echo "<form action=\"index.php\" method=\"GET\">";
    echo "Masukkan jumlah k : <input type='number' min='1' step='1' name='totalK'>";
    echo "<input type=\"submit\" value=\"Submit\" name=\"submitTotalK\">";
    echo "</form> <br>";

    if(isset($_POST['submitCsv'])){
        $path = $_FILES["fileToUpload"]["tmp_name"];
        $_SESSION['csv'] = readCSV($path);  //save csv to session
        $_SESSION['totalFeature'] = count($_SESSION['csv'][0]); //save total number of features to session
        $_SESSION['totalRow'] = count($_SESSION['csv']) - 1; //save total number of data to session
        $_SESSION['features'] = ""; //initialize names of features

        //iterate all features name
        for($i=0;$i<$_SESSION['totalFeature'];$i++){
            if($i == 0){
                $_SESSION['features'] = $_SESSION['csv'][0][$i];
            }else{
                $_SESSION['features'] = $_SESSION['features'] . ", " . $_SESSION['csv'][0][$i];
            }
        }

    }

    if(!isset($_SESSION['csv']) && isset($_GET['submitTotalK'])){
        alert('Silahkan upload CSV terlebih dahulu');
    }

    //save total kluster to session
    if (isset($_GET['submitTotalK'])){
        $_SESSION['totalK'] = $_GET['totalK'];
    }

    if(isset($_SESSION['csv'])){
        echo "Jumlah fitur : ". $_SESSION['totalFeature'] . "<br>";
        echo "Jumlah data : ". $_SESSION['totalRow'] . "<br>";
        echo "Fitur-fitur : (".$_SESSION['features'].") <br>";

        if(isset($_SESSION['totalK'])){
            echo "<form action='' method='get'>";
            for($i=1;$i<=$_SESSION['totalK'];$i++){
                echo "<br>";
                echo "Kluster ".$i."<br>";
                for($j=0;$j<$_SESSION['totalFeature'];$j++){
                    echo $_SESSION['csv'][0][$j]. " : <input type='number' min=0 name='".$_SESSION['csv'][0][$j].$i."'><br>";
                }
                echo "<br><br>";
            }
            echo "<input type='submit' value='Jalankan Algoritma' name='proceedKmeans'>";
            echo "</form>";
        }

        echo "<table align='center' style=\"border: 1px solid black\">";
        echo "<tr>";
        echo "<th style='border: 1px solid black'>No.</th>";
        for($i=0;$i<sizeof($_SESSION['csv'][0]);$i++){
            echo "<th style='border: 1px solid black'>".$_SESSION['csv'][0][$i]."</th>";
        }
        echo "</tr>";

        for($i=1;$i<=$_SESSION['totalRow'];$i++){
            echo "<tr>";
            echo "<td style='border: 1px solid black'>".$i."</td>";
            for($j=0;$j<$_SESSION['totalFeature'];$j++){
                echo "<td style='border: 1px solid black'>";
                echo $_SESSION['csv'][$i][$j];
                echo "</td>";
            }
            echo "</tr>";
        }

        echo "</table> <br>";

    }
?>
