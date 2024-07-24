<?php

namespace FIZ;

use Exception;
use Module\Archival\Archive;
use Throwable;

abstract class Experiments
{
    public static function archiveTest(): array
    {
        $reposFile = file_get_contents('FIZ/queryResults.csv');
        $reposFile = preg_replace('$"$',"", $reposFile);
        $reposArray = explode("\n", $reposFile);

        $counter = 0;
        echo "Repos Number: ". count($reposArray);
        $failed = [];

        $start = microtime(true);
        foreach($reposArray as $repo){
            try{
                $archivalInitialResponse = Archive::Repository($repo);
                if($archivalInitialResponse instanceof Throwable){
                    $failed[] = $repo;
                    continue;
                }
                $counter++;
                file_put_contents("FIZ/requestIDs.txt", $repo.",". $archivalInitialResponse["id"]."\n", flags: FILE_APPEND);

            }catch(Exception $e){
                $failed[] = $repo;
                continue;
            }
        }
        $stop = microtime(true);
        echo "Elapsed Time: ".round($stop - $start, 2)." seconds\n";
        echo "Repos Archived: $counter"."\n";

        return $failed;
    }

    public static function getSwhIDs(): array
    {
        copy('FIZ/requestIDs.txt', 'FIZ/requestIDs-processing.txt');

        $counter = 0;
        $failed = [];

        $start = microtime(true);

        do{
            $requests = file_get_contents('FIZ/requestIDs-processing.txt');
            $requestsArray = explode("\n", $requests);
            array_pop($requestsArray);

            echo "Archival requests Number in file: ". count($requestsArray);

            foreach ($requestsArray as $request)
            {
                try{
                    $repoData = explode(',', $request); // url and id
                    $url = $repoData[0];
                    $saveRequestID = $repoData[1];

                    $archiveStatus = new Archive($url);

                    $statusResult = $archiveStatus->getArchivalStatus($saveRequestID);

                    $requests = preg_replace("#".$request."\n#" , "" , $requests);
                    file_put_contents('FIZ/requestIDs-processing.txt', $requests);   // delete (override) from the processing file

                    if($statusResult instanceof Throwable || $statusResult['save_task_status'] === 'failed'){
                        $failed[] = $url;
                        file_put_contents("FIZ/requestIDs-failed.txt", $url."=>". $saveRequestID."\n", flags: FILE_APPEND);

                        continue;
                    }

                    if($statusResult['snapshot_swhid'] !== NULL){
                        file_put_contents("FIZ/swhIDs.txt", $url."=>". $statusResult['contextual_swh_ids']['Directory-Context']."\n", flags: FILE_APPEND);
                        $counter++;
                    }


                } catch (Exception $e){
                    $failed[] = $request;
                    continue;
                }
            }

        }while(count($requestsArray) > 0);


        $stop = microtime(true);
        echo "Elapsed time: ".round($stop - $start, 2)." seconds\n";
        echo "Repos Tracked: $counter"."\n";

        return $failed;

    }

}