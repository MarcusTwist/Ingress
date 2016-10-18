<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ToJsonController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        return view('tojson');
    }

    public function store(Request $request)
    {
        $bookmarks = $request->get('');
        $lines = $request->get('links');
        $result = $this->process($bookmarks, $lines);
        
        return view('toJson')->with(['links' => $result]);
    }

    public function process($bookmarks, $lines)
    {
        $bookmarks = $this->processBookmarks($bookmarks);
        $links = $this->processLines($lines);
        $result = $this->join($bookmarks, $links);

        return view('toJsonResult')->with(['links' => $result]);
    }

    public function processBookmarks($bookmarks)
    {
        $bookmarks = json_decode($bookmarks);
        $portal = [];

        foreach ($bookmarks->portals as $anker) {
            $label = $anker->label;
            $portal[$label]['name'] = $anker->label;

            foreach ($anker->bkmrk as $key) {
                $portal[$label]['portal'][] = $key;
            }
        }
        return $portal;
    }

    public function processLines($rawFields)
    {
        $fields = json_decode($rawFields);
        $ii=3;
        $portal = 1;
        $i = $city = 0;
        $iterationsTotal = (count($fields)-1)*count($fields);
        
        for ($cp=1; $cp < count($fields)+1; $cp++) { 
            
            for ($cf=0; $cf <count($fields) ; $cf++) { 
                if($i==0){
                    $to['city'] = $city+1;
                    $to['portal'] = $portal-1;
                    $links[]['to']['coordinates'] = $fields[$to['portal']]->latLngs[$to['city']]; //1B
                    $i++;
                }elseif($i==1){
                    $to['city'] = $city+2;
                    $to['portal'] = $portal-1;
                    $links[]['to']['coordinates'] = $fields[$to['portal']]->latLngs[$to['city']]; //1B
                    $i++;
                }elseif($i==2){
                    $to['city'] = $city;
                    $to['portal'] = $portal;
                    if ( !isset($fields[$to['portal']])){
                        break;
                    }
                    $links[]['to']['coordinates'] = $fields[$to['portal']]->latLngs[$to['city']]; //1B
                    $i++;
                }elseif($i==3){
                    $to['city'] = $city+2;
                    $to['portal'] = $portal-1;
                    $links[]['to']['coordinates'] = $fields[$to['portal']]->latLngs[$to['city']]; //1B
                    $i++;
                }elseif($i==4){
                    $to['city'] = $city;
                    $to['portal'] = $portal;
                    $links[]['to']['coordinates'] = $fields[$to['portal']]->latLngs[$to['city']]; //1B
                    $i++;
                }elseif($i==5){
                    $to['city'] = $city+1;
                    $to['portal'] = $portal;
                    $links[]['to']['coordinates'] = $fields[$to['portal']]->latLngs[$to['city']]; //1B
                    $i=0;
                }
            }
            $portal++;
        }
        
        $portal = 1;
        $iterations = $city = 0;
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

    public function join($bookmarks, $links) 
    {
        $portals = $this->bookmarksToPortals($bookmarks);
        
        for ($i=0; $i < count($links); $i++) { 
            $links[$i]['from']['latLng'] = $links[$i]['from']['coordinates']->lat. ','. $links[$i]['from']['coordinates']->lng;
            $links[$i]['to']['latLng'] = $links[$i]['to']['coordinates']->lat. ','. $links[$i]['to']['coordinates']->lng;
            $links[$i]['from']['name'] = array_search($links[$i]['from']['latLng'], array_column($bookmarks, 'label'));
           
            $portalfrom = $this->findPortal($links[$i]['from']['latLng'], $portals);
            $links[$i]['from']['name'] = $portalfrom->label;
            $links[$i]['from']['anker'] = $portalfrom->anker;
           
            $portalto = $this->findPortal($links[$i]['to']['latLng'], $portals);
            $links[$i]['to']['name'] = $portalto->label;
            $links[$i]['to']['anker'] = $portalto->anker;
        }
        return $links;
    }

    public function bookmarksToPortals($bookmarks)
    {
        foreach ($bookmarks as $citys) {

            foreach ($citys as $city) {

                if ( is_array($city) ) {    

                    foreach ($city as $portal) {
                        $portal->anker = $citys['name'];
                        $portals[] = $portal;
                    }
                }
            }
        }

        return $portals;
    }

    public function findPortal($latlng, $portalList)
    {
        foreach ($portalList as $portal) {

            if ($portal->latlng == $latlng) {
                return $portal;
            }
        }

        return 'no portal found';
    }
}
