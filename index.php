<?php
    session_start();

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


    //form for uploading csv file
    echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\">
          Select csv file to upload:
          <input type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\">
          <input type=\"submit\" value=\"Upload CSV\" name=\"submitCsv\">
          </form>";
    //for for uploading csv file

    //reset csv form
    echo "<form action=\"index.php\" method=\"get\">
          <input type=\"submit\" value=\"RESET\" name=\"submitReset\">
          </form><br><br>";
    //reset csv form

    //form for entering total number of k
    echo "<form action=\"index.php\" method=\"GET\">";
    echo "Masukkan jumlah k : <input type='number' min='1' step='1' name='totalK' value='3'>";
    echo "<input type=\"submit\" value=\"Submit\" name=\"submitTotalK\">";
    echo "</form> <br>";
    //form for entering total number of k

    //unset all session variable
    if(isset($_GET['submitReset'])){
        session_unset();
        session_destroy();
        //echo "<script type=\"text/javacript\">location.reload();</script>";
    }
    //unset all session variable

    //do something when csv is submitted
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
    //do something when csv is submitted

    //alert if csv is not set but total k is submitted
    if(!isset($_SESSION['csv']) && isset($_GET['submitTotalK'])){
        alert('Silahkan upload CSV terlebih dahulu');
    }
    //alert if csv is not set but total k is submitted

    //save total kluster to session
    if (isset($_GET['submitTotalK'])){
        $_SESSION['totalK'] = $_GET['totalK'];
    }
    //save total k to session

    //do something when csv is set in session
    if(isset($_SESSION['csv'])){
        //show dataset details
        echo "Jumlah fitur : ". $_SESSION['totalFeature'] . "<br>";
        echo "Jumlah data : ". $_SESSION['totalRow'] . "<br>";
        echo "Fitur-fitur : (".$_SESSION['features'].") <br>";
        //show dataset details

        //show clusters input form if number of total K is set
        if(isset($_SESSION['totalK'])){

            $valueForKluster1 = array(16, 8, 380, 151, 4000, 13, 1975);
            $valueForKluster2 = array(21, 6, 210, 100, 3100, 16, 1979);
            $valueForKluster3 = array(31, 4, 120, 80, 2200, 18, 1977);

            echo "<form action='' method='get'>";
            for($i=1;$i<=$_SESSION['totalK'];$i++){
                echo "<br>";
                echo "<table align='center' style=\"border: 1px solid black\">";
                echo "<tr><th colspan='2' style=\"border: 1px solid black\">Kluster ".$i."</th></tr>";
                for($j=0;$j<$_SESSION['totalFeature'];$j++){
                    if($i == 1){
                        echo "<tr><td style=\"border: 1px solid black\">".$_SESSION['csv'][0][$j]. "</td><td> <input type='number' min=0 name='".$_SESSION['csv'][0][$j].$i."' value='".$valueForKluster1[$j]."'></td></tr>";
                    }elseif($i == 2){
                        echo "<tr><td style=\"border: 1px solid black\">".$_SESSION['csv'][0][$j]. "</td><td> <input type='number' min=0 name='".$_SESSION['csv'][0][$j].$i."' value='".$valueForKluster2[$j]."'></td></tr>";
                    }elseif ($i == 3){
                        echo "<tr><td style=\"border: 1px solid black\">".$_SESSION['csv'][0][$j]. "</td><td> <input type='number' min=0 name='".$_SESSION['csv'][0][$j].$i."' value='".$valueForKluster3[$j]."'></td></tr>";
                    }else{
                        echo "<tr><td style=\"border: 1px solid black\">".$_SESSION['csv'][0][$j]. "</td><td> <input type='number' min=0 name='".$_SESSION['csv'][0][$j].$i."'></td></tr>";
                    }
                }
                echo "</table>";
                echo "<br>";
            }
            echo "<table align='center'><tr><th><input align='middle' type='submit' value='Jalankan Algoritma' name='proceedKmeans'></th></tr></table>";
            echo "</form>";
        }
        //show clusters input form if number of total K is set

        //do kmean
        if(isset($_GET['proceedKmeans'])){
            //save kluster in $_SESSION['k1'], $_SESSION['k2'], and so on
            for($i=1;$i<=$_SESSION['totalK'];$i++){
                $_SESSION['k'.$i] = array();
                for($j=0;$j<$_SESSION['totalFeature'];$j++){
                    array_push($_SESSION['k'.$i], $_GET[$_SESSION['csv'][0][$j].$i]);
                }
            }
            //save kluster in $_SESSION['k1'], $_SESSION['k2'], and so on

            $counter = 0;

            $isFinished = false;
            while (!$isFinished){
                $counter++;
                for($i=1;$i<=$_SESSION['totalK'];$i++){
                    $_SESSION['tmpKluster'.$i] = array(); //for storing the new kluster
                    for($j=0;$j<$_SESSION['totalFeature'];$j++){
                        array_push($_SESSION['tmpKluster'.$i], 0);
                    }
                    $_SESSION['k'.$i.'index'] = array(); //for storing corresponding data point's index to its kluster
                }

                echo "<h1 align='center'>Iterasi ke-".$counter."</h1><br>";

                //determine all data point's kluster
                //for($i=1;$i<=$_SESSION['totalRow'];$i++){
                for($i=1;$i<=50;$i++){
                    $tmp_results = array();

                    echo "<h2 align='center'>Data ke-".$i."</h2><br>";

                    echo "<table align='center' style=\"border: 1px solid black\">";
                    echo "<tr>";
                    echo "<th style=\"border: 1px solid black\">No.</th>";
                    echo "<th style=\"border: 1px solid black\">Jarak dengan kluster</th>";
                    echo "<th style=\"border: 1px solid black\">Result</th>";
                    echo "</tr>";
                    //do each data point againts every kluster
                    for($j=1;$j<=$_SESSION['totalK'];$j++){
                        $tmp_distance = 0;

                        echo "<tr>";
                        echo "<td style=\"border: 1px solid black\">".$j."</td>";
                        echo "<td style=\"border: 1px solid black\">&#8730;";
                        //calculate distance
                        for($k=0;$k<$_SESSION['totalFeature'];$k++){
                            if($k == 0){
                                echo "(".$_SESSION['csv'][$i][$k]." - ".$_SESSION['k'.$j][$k].")<sup>2</sup>";
                            }else{
                                echo "+ (".$_SESSION['csv'][$i][$k]." - ".$_SESSION['k'.$j][$k].")<sup>2</sup>";
                            }
                            $tmp_distance = $tmp_distance + pow(($_SESSION['csv'][$i][$k] - $_SESSION['k'.$j][$k]), 2);
                        }
                        //calculate distance
                        echo "</td>";

                        echo "<td style=\"border: 1px solid black\">".$tmp_distance."</td>";

                        echo "</tr>";

                        array_push($tmp_results, pow($tmp_distance, 0.5)); //store distance in tmp_result which later will be compared to determine the minimum
                    }
                    //do each data point againts every kluster

                    echo "</table> <br>";

                    $tmp_key = array_search(min($tmp_results), $tmp_results) + 1;
                    array_push($_SESSION['k'.$tmp_key.'index'], $i);
                }
                //determine all data point's kluster

                echo "<h1 align='center'>Kluster Baru</h1><br>";
                echo "<table align='center' style=\"border: 1px solid black\">";
                echo "<tr><th style=\"border: 1px solid black\">No.</th>";
                for($i=0;$i<$_SESSION['totalFeature'];$i++){
                    echo "<th style=\"border: 1px solid black\">".$_SESSION['csv'][0][$i]."</th>";
                }
                echo "</tr>";

                //calculate new klusters
                for($i=1;$i<=$_SESSION['totalK'];$i++){
                    echo "<tr>";
                    echo "<td style=\"border: 1px solid black\">$i</td>";
                    for($j=0;$j<count($_SESSION['k'.$i.'index']);$j++){
                        for($k=0;$k<$_SESSION['totalFeature'];$k++){
                            $_SESSION['tmpKluster'.$i][$k] = $_SESSION['tmpKluster'.$i][$k] + $_SESSION['csv'][$_SESSION['k'.$i.'index'][$j]][$k] / count($_SESSION['k'.$i.'index']);
                        }
                    }
                    for($j=0;$j<$_SESSION['totalFeature'];$j++){
                        echo "<td style=\"border: 1px solid black\">".$_SESSION['tmpKluster'.$i][$j]."</td>";
                    }
                    echo "</tr>";
                }
                //calculate new klusters

                echo "</table>";

                $oldKlusters = array();
                $newKlusters = array();
                //wrap all klusters in an array for comparison
                for($i=1;$i<=$_SESSION['totalK'];$i++){
                    array_push($oldKlusters, $_SESSION['k'.$i]);
                    array_push($newKlusters, $_SESSION['tmpKluster'.$i]);
                }

                if($oldKlusters == $newKlusters){
                    $isFinished = true;
                    echo "<br>";
                }else{
                    //update old klusters
                    for($i=1;$i<=$_SESSION['totalK'];$i++){
                        $_SESSION['k'.$i] = $_SESSION['tmpKluster'.$i];
                    }
                    //update old klusters
                }

                if($counter == 20){
                    $isFinished = true;
                    alert("MORE THAN 10");
                }
            }
        }
        //do kmean

        //show all data points
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
        //show all data points

    }
?>
