<?php

namespace App\Feeds;

use DOMXPath;
use DOMDocument;
use Carbon\Carbon;

// The som of fields is: Fields = portals - 2
// The som of links is: Links =( fields ×2 ) +1

class BaseFactory
{
    //Give a array of fields and
    //Return a array of links
    public function makeRose($fields)
    {
        $ii=3;
        $portal = 1;
        $i = $city = 0;
        $iterationsTotal = (count($fields)-1)*count($fields);
        $first = true;
        if ($first) {
            $links[]['to']['coordinates'] = $fields[0]->latLngs[1];   //2A
            $links[]['to']['coordinates'] = $fields[0]->latLngs[2];  //3A
            $links[]['to']['coordinates'] = $fields[0]->latLngs[2];  //3A
            $first = false;
        }

        for ($cp=1; $cp < count($fields)+1; $cp++) { 
                
            for ($cf=0; $cf <count($fields) ; $cf++) { 
                if($i==0){
                    $to['city'] = $city+1;
                    $to['portal'] = $portal-1;
                    $links[]['to']['coordinates'] = $fields[$to['portal']]->latLngs[$to['city']];
                    $i++;
                }elseif($i==1){
                    $to['city'] = $city+2;
                    $to['portal'] = $portal-1;
                    $links[]['to']['coordinates'] = $fields[$to['portal']]->latLngs[$to['city']];
                    $i++;
                }elseif($i==2){
                    $to['city'] = $city;
                    $to['portal'] = $portal;
                    if ( !isset($fields[$to['portal']])){
                        break;
                    }
                    $links[]['to']['coordinates'] = $fields[$to['portal']]->latLngs[$to['city']];
                    $i++;
                }elseif($i==3){
                    $to['city'] = $city+2;
                    $to['portal'] = $portal-1;
                    $links[]['to']['coordinates'] = $fields[$to['portal']]->latLngs[$to['city']];
                    $i++;
                }elseif($i==4){
                    $to['city'] = $city;
                    $to['portal'] = $portal;
                    $links[]['to']['coordinates'] = $fields[$to['portal']]->latLngs[$to['city']];
                    $i++;
                }elseif($i==5){
                    $to['city'] = $city+1;
                    $to['portal'] = $portal;
                    $links[]['to']['coordinates'] = $fields[$to['portal']]->latLngs[$to['city']];
                    $i=0;
                }
            }
            $portal++;
        }
        $portal = 1;
        $iterations = $city = 0;
        $first = true;
        if ($first) {
            $links[$iterations]['from']['coordinates'] = $fields[0]->latLngs[0]; //1A
            $iterations++;
            $links[$iterations]['from']['coordinates'] = $fields[0]->latLngs[0]; //1A
            $iterations++;
            $links[$iterations]['from']['coordinates'] = $fields[0]->latLngs[1];  //2A
            $iterations++;
            $first = false;
        }
        $links[$iterations]['from']['coordinates'] = $fields[$portal]->latLngs[$city]; //1B
            
        for ($i=1; $i < $iterationsTotal+1; $i++) { 
            $links[$iterations]['from']['coordinates'] = $fields[$portal]->latLngs[$city]; //1B
                
            if ( $i% 2==0) {
                $city++;
                if ($city == 3){
                    $city=0;
                }
            } 

            if ($i% 6 ==0) {
                $portal++;
                if ($portal == 6){
                    $portal = 0;
                }
            }
            $iterations++;
        }

        $unset = count($links)-1;
        unset($links[$unset]);
        $unset = $unset-1;
        unset($links[$unset]);

        return $links;
    }

    public function makeOnion($rawFields)
    {
        //dd($rawFields);
        $fields = $rawFields;
//        $ankers = [1,2,3];

        $home= $ii= $portal = $anker = 0;
        
        $links[$ii]['from']['coordinates'] = $fields[0]->latLngs[0];
        $links[$ii]['to']['coordinates'] = $fields[1]->latLngs[0];
        $ii++;
        
        for ($home=0; $home < 2; $home++) { 
            for ($i=0; $i <count($fields)-1 ; $i++) { 
                if ($home == 0 ){
                    $links[$ii]['from']['coordinates'] = $fields[$home]->latLngs[0];
                    $links[$ii]['to']['coordinates'] = $fields[$i]->latLngs[2];
                } elseif ($home == 1) {
                    $links[$ii]['from']['coordinates'] = $fields[0]->latLngs[1];
                    //$links[$ii]['to']['coordinates'] = $fields[$i]->latLngs[count($fields)-1];
                    $links[$ii]['to']['coordinates'] = $fields[$i]->latLngs[1];
                }
                $ii++;
            }
            if ($home == 1) {
                for ($i=1; $i <count($fields) ; $i++) { 
                    $links[$ii]['from']['coordinates'] = $fields[0]->latLngs[1];
                    $links[$ii]['to']['coordinates'] = $fields[$i]->latLngs[0];
                    $ii++;
                }

                for ($i=1; $i <count($fields) ; $i++) { 
                    $links[$ii]['from']['coordinates'] = $fields[count($fields)-1]->latLngs[2];
                    $links[$ii]['to']['coordinates'] = $fields[$i]->latLngs[0];
                    $ii++;
                }

                for ($i=1; $i <count($fields) ; $i++) { 
                    $links[$ii]['from']['coordinates'] = $fields[count($fields)-1]->latLngs[0];
                    $links[$ii]['to']['coordinates'] = $fields[$i]->latLngs[1];
                    $ii++;
                }

                for ($i=1; $i <count($fields) ; $i++) { 
                    $links[$ii]['from']['coordinates'] = $fields[count($fields)-1]->latLngs[2];
                    $links[$ii]['to']['coordinates'] = $fields[$i]->latLngs[1];
                    $ii++;
                }
            }
        }
        return $links;
    }

    public function before($first, $inthat)
    {
        return substr($inthat, 0, strpos($inthat, $first));
    }

    public function before_last($first, $inthat)
    {
        return substr($inthat, 0, $this->strrevpos($inthat, $first));
    }

    public function after($first, $inthat)
    {
        if (!is_bool(strpos($inthat, $first))) {
            return substr($inthat, strpos($inthat, $first)+strlen($first));
        }
    }

    public function after_last($first, $inthat)
    {
        if (!is_bool($this->strrevpos($inthat, $first))) {
            return substr($inthat, $this->strrevpos($inthat, $first)+strlen($first));
        }
    }

    public function between($first, $that, $inthat)
    {
        return $this->before($that, $this->after($first, $inthat));
    }

    public function between_last($first, $that, $inthat)
    {
        return $this->after_last($first, $this->before_last($that, $inthat));
    }

    public function strrevpos($instr, $needle)
    {
        $rev_pos = strpos(strrev($instr), strrev($needle));
        if ($rev_pos===false) {
            return false;
        } else {
            return strlen($instr) - $rev_pos - strlen($needle);
        }
    }

    public function arrayTrim($array)
    {
        foreach ($array as $key) {
            $trimmed[] = trim($key);
        }
        return $trimmed;
    }

    public function cleanText($text)
    {
        $text = $this->removeNewLines($text);
        $text = $this->removeTabs($text);
        $text = $this->replaceSpaces($text);
        $text = preg_replace("/&#?[a-z0-9]+;/i", "", $text);
        return trim($text);
    }

    public function removeNewLines($value)
    {
        return str_replace(array("\r\n", "\r", "\n"), "", $value);
    }

    public function removeTabs($value)
    {
        return preg_replace('/\t+/', '', $value);
    }

    public function replaceSpaces($value)
    {
        return preg_replace('/\s+/', '', $value);
    }

    public function keepOneSpace($value)
    {
        return preg_replace("/[ ]{2,}/", " ", $value);
    }

    public function removeHtmlSpace($string)
    {
        $string = htmlentities($string, null, 'utf-8');
        $string = str_replace(["&nbsp;", "nnbsp;", "amp;"], "", $string);
        return trim($string);
    }

    public function aasort (&$array, $key) {
        $sorter=array();
        $ret=array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii]=$va[$key];
        }
        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii]=$array[$ii];
        }
        $array=$ret;
        return $array;
    }
}
