<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function post_message(Request $request)
    {
        if (!Session::has('sessionToken')) $this->start_session($request);
        if (strpos(strtolower($request->getContent()), 'force')) {
            try {
                $answer = $this->list_content('films', 'title');
                return $this->parse_movies($answer);
            } catch (\Exception $e) { 
                return response()->json(['error' => $e->getMessage()], 503); 
            }
        }
        $body = $this->send_message($request);
        if (isset($body->errors)) {
            try {
                $this->start_session($request);
                $body = $this->send_message($request);
            } catch (\Exception $e) { 
                return response()->json(['error' => $e->getMessage()], 503); 
            }
        }
        if (isset($body->answers[0]->flags[0]) && Session('flag') == 'no-results') {
            try {
                $answer = $this->list_content('people', 'name');
                return $this->parse_people($answer);
            } catch (\Exception $e) { 
                return response()->json(['error' => $e->getMessage()], 503); 
            }
        }
        Session::put('answer', $body->answers[0]->message);
        if (isset($body->answers[0]->flags[0])) {
            Session::put('flag', $body->answers[0]->flags[0]);
        }
        $response = [
            'sessionID' => Session::getId(),
            'answer' => ['user' => 'yoda', 'message' => $body->answers[0]->message]
        ];
        return $response;
    }

    private function send_message($request)
    {
        $data = ['message' => $request->getContent()];
        $headers = [
            'x-inbenta-key' => $request->header('api-key'),
            'Authorization' => 'Bearer '.Session('accessToken'),
            'x-inbenta-session' => 'Bearer '.Session('sessionToken'),
        ];
        $url = Session('chatbotApi').env('API_VERSION').'/conversation/message';
        $response = Http::withHeaders($headers)->post($url, $data);
        $body = json_decode($response->getBody());
        return $body;
    }

    private function start_session($request)
    {
        $data = ['secret' => env('API_SECRET')];
        $headers = [
            'x-inbenta-key' => $request->header('api-key'),
            'Authorization' => 'Bearer '.Session('accessToken')
        ];
        $url = Session('chatbotApi').env('API_VERSION').'/conversation';
        $response = Http::withHeaders($headers)->post($url, $data);
        $body = json_decode($response->getBody());
        Session::put('sessionToken', $body->sessionToken);
    }

    private function list_content($search, $field)
    {
        $data = [
            'query' => '{all'.ucfirst($search).'(first:10)
                {'.$search.'{'.$field.'}}}'
        ];
        $url = env('STAR_WARS_API');
        try {
            $response = Http::get($url, $data);
            $body = json_decode($response->getBody());
            return $body;
        } catch (\Exception $e) {
            report($e);
            return;
        }
    }

    private function parse_people($answer)
    {
        $people_array = $answer->data->allPeople->people;
        $people_string = "I haven't found any results, 
        but here is a list of some Star Wars characters:<br>";
        foreach ($people_array as $person) {
            $people_string = $people_string."- ".$person->name."<br>";
        }
        $response = [
            'sessionID' => Session::getId(),
            'answer' => ['user' => 'yoda', 'message' => $people_string]];
        Session::forget('flag');
        return $response;
    }

    private function parse_movies($answer)
    {
        $films_array = $answer->data->allFilms->films;
        $films_string = "The Force is in this movies:<br>";
        foreach ($films_array as $film) {
            $films_string = $films_string."- ".$film->title."<br>";
        }
        $response = [
            'sessionID' => Session::getId(),
            'answer' => ['user' => 'yoda', 'message' => $films_string]];
        return $response;
    }
}
