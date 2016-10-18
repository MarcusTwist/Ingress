<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Feeds\BaseFactory;

class OnionController extends Controller
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
        
        return view('onion');
    }

    public function store(Request $request)
    {
        $bookmarks = $request->get('bookmarks');
        $lines = $request->get('links');
        $version = $request->get('version');

        $linkplan = $this->process($bookmarks, $lines, $version);
        $drawtools = $this->toJson($linkplan);
        $intel = $this->makeIntelLink($linkplan);

        $view = view::make('onionResult')->with(['links' => $linkplan, 'drawtools' => $drawtools, 'bookmarks' => $bookmarks, 'intel' => $intel, 'version' => $version]);

        return $view;
    }

    public function process($bookmarks, $lines, $version)
    {
        $bookmarks = $this->processBookmarks($bookmarks);

        if ($version == 'Rose') {
            $links = $this->processRose($lines);
        } elseif ($version == 'Onion') {
            $links = $this->processOnion($lines);
        }
        $result = $this->join($bookmarks, $links);
        
        return $result;
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

    public function processOnion($rawFields)
    {
        $fields = json_decode($rawFields);
        $factory = new BaseFactory;
        $result = $factory->makeOnion($fields);
        return $result;
    }


    public function processRose($rawFields)
    {
        $fields = json_decode($rawFields);
        $factory = new BaseFactory;
        $result = $factory->makeRose($fields);
        
        return $result;
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

    public function toJson($linkplan)
    {
        foreach ($linkplan as $link) {
            $linksObject[] = $this->toDrawArray($link);
        }
        $linksObject = json_encode($linksObject);

        return $linksObject;
    }

    private function toDrawArray($link)
    {
        $singleLink = (object)[];
        $singleLink->type = 'polygon';
        $singleLink->latLngs[0] = $link['from']['coordinates'];
        $singleLink->latLngs[1] = $link['to']['coordinates'];
        $singleLink->color = '#a24ac3';
        return $singleLink;
    }

    public function makeIntelLink($portals)
    {
        $ii=0;
        foreach ($portals as $fromTo) {
            foreach ($fromTo as $portal) {
                $newPortal[$ii]['anker'] = $portal['anker'];
                $newPortal[$ii]['name'] = $portal['name'];
                $newPortal[$ii]['intel'] = 'https://www.ingress.com/intel?ll='. $portal['latLng'] .'&z=17&pll='. $portal['latLng'];
                $newPortal[$ii]['maps'] = 'https://www.google.com/maps?q='. $portal['latLng'];
                $ii++;
            }
        }
        $unique = array_unique($newPortal, SORT_REGULAR);
        $factory = new BaseFactory;
        $unique = $factory->aasort($unique, 'anker');
        $unique = array_values(($unique));
        return $unique;
    }
}
