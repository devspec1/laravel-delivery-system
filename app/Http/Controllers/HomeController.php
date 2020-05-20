<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller as BaseController;
use Auth;
use App\Models\Pages;
use App\Models\Help;
use App\Models\HelpSubCategory;
use App\Models\HelpTranslations;
use App\Models\Currency;
use App\Models\JoinUs;
use App\Models\User;
use Session;
use Route;
use DB;

class HomeController extends BaseController
{
    
	public function index(){

        if (Auth::check()) {
            $data['result'] = User::find(@Auth::user()->id);
            return view('driver_dashboard.new_dash',$data);
        }
        else {
             return view('driver_dashboard.new_login');
        }

    	
        
    }    	  

  
    /**
     * View Static Pages
     *
     * @param array $request  Input values
     * @return Static page view file
     */
    public function static_pages(Request $request)
    {
        if($request->token!='')
        {
         Session::put('get_token',$request->token); 

        }
        $pages = Pages::where(['url'=>$request->name, 'status'=>'Active']);

        if(!$pages->count())
            abort('404');

        $pages = $pages->first();

        $data['content'] = str_replace(['SITE_NAME', 'SITE_URL'], [SITE_NAME, url('/')], $pages->content);
        $data['title'] = $pages->name;

        return view('home.static_pages', $data);
    }

    /**
     * Set session for Currency & Language while choosing footer dropdowns
     *
     */
    public function set_session(Request $request)
    {
        if($request->currency) {
            Session::put('currency', $request->currency);
            $symbol = Currency::original_symbol($request->currency);
            Session::put('symbol', $symbol);
        }
        else if ($request->language) {
            Session::put('language', $request->language);
            App::setLocale($request->value);

        }
    }

    public function help(Request $request) {

        if ($request->token != '') {
            Session::put('get_token', $request->token);

        }

        if (Route::current()->uri() == 'help') {
            $data['result'] = Help::whereSuggested('yes')->whereStatus('Active')->get();
            //$data['token']  =$request->token;
        } elseif (Route::current()->uri() == 'help/topic/{id}/{category}') {
            $count_result = HelpSubCategory::find($request->id);
            $data['subcategory_count'] = $count = (str_slug($count_result->name, '-') != $request->category) ? 0 : 1;
            $data['is_subcategory'] = (str_slug($count_result->name, '-') == $request->category) ? 'yes' : 'no';
            if ($count) {
                $data['result'] = Help::whereSubcategoryId($request->id)->whereStatus('Active')->get();
            } else {
                $data['result'] = Help::whereCategoryId($request->id)->whereStatus('Active')->get();
            }

        } else {
            $data['result'] = Help::whereId($request->id)->whereStatus('Active')->get();
            $data['is_subcategory'] = ($data['result'][0]->subcategory_id) ? 'yes' : 'no';
        }

        $data['category'] = Help::with(['category', 'subcategory'])->whereStatus('Active')->groupBy('category_id')->get(['category_id', 'subcategory_id']);

        return view('home.help', $data);
    }

    public function ajax_help_search(Request $request) {
        $term = $request->term;

        $queries = Help::where('question', 'like', '%' . $term . '%')->get();
        $queries_translate = HelpTranslations::where('name', 'like', '%' . $term . '%')->get();
        if ($queries->isEmpty() && $queries_translate->isEmpty()) {
            $results[] = ['id' => '0', 'value' => trans('messages.help.no_results_found'), 'question' => trans('messages.help.no_results_found')];
        } else {
            foreach ($queries as $query) {
                $results[] = ['id' => $query->id, 'value' => str_replace('SITE_NAME', SITE_NAME, $query->question), 'question' => str_slug($query->question, '-')];
            }
            foreach ($queries_translate as $translate) {
                $results[] = ['id' => $translate->help_id, 'value' => str_replace('SITE_NAME', SITE_NAME, $translate->name), 'question' => str_slug($translate->name, '-')];
            }
        }

        return json_encode($results);
    }

    public function clearLog()
    {
        exec('echo "" > ' . storage_path('logs/laravel.log'));
    }

    public function showLog()
    {
        $contents = \File::get(storage_path('logs/laravel.log'));
        echo '<pre>'.$contents.'</pre>';
    }

    public function updateEnv(Request $request)
    {
        $requests = $request->all();
        $valid_env = ['APP_ENV','APP_DEBUG'];
        foreach ($requests as $key => $value) {
            $prev_value = getenv($key);
            logger($key.' - '.$prev_value);
            if(in_array($key,$valid_env)) {
                updateEnvConfig($key,$value);
            }
        }
    }

    public function exportDB()
    {
        $targetTables = [];
        $newLine = "\r\n";

        $targetTables  = array_map('reset', \DB::select('SHOW TABLES'));

        foreach($targetTables as $table){
            $tableData = DB::select(DB::raw('SELECT * FROM '.$table));
            $res = DB::select(DB::raw('SHOW CREATE TABLE '.$table));

            $cnt = 0;
            $temp_result = (json_decode(json_encode($res[0]), true));
            $content = (!isset($content) ?  '' : $content) . $temp_result["Create Table"].";" . $newLine . $newLine;

            foreach($tableData as $row){
                $subContent = "";
                $firstQueryPart = "";
                if($cnt == 0 || $cnt % 100 == 0){
                    $firstQueryPart .= "INSERT INTO {$table} VALUES ";
                    if(count($tableData) > 1)
                        $firstQueryPart .= $newLine;
                }

                $valuesQuery = "(";
                foreach($row as $key => $value){
                    $valuesQuery .= $value . ", ";
                }

                $subContent = $firstQueryPart . rtrim($valuesQuery, ", ") . ")";

                if( (($cnt+1) % 100 == 0 && $cnt != 0) || $cnt+1 == count($tableData))
                    $subContent .= ";" . $newLine;
                else
                    $subContent .= ",";

                $content .= $subContent;
                $cnt++;
            }
            $content .= $newLine;
        }

        $content = trim($content);

        $backup_name = env('DB_DATABASE').".sql";
        header('Content-Type: application/octet-stream');   
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"".$backup_name."\"");  
        echo $content; exit;
    }

    /**
     * Redirect to play store or app store based on OS
     *
     * @param array $request  Input values
     * @return Static page view file
     */
    public function redirect_to_app(Request $request)
    {
        $join_us = JoinUs::get();
        if($request->type == 'driver') {
            $play_store_link = $join_us->where('name','play_store_driver')->first()->value;
            $app_store_link  = $join_us->where('name','app_store_driver')->first()->value;
        }
        else {
            $play_store_link = $join_us->where('name','play_store_rider')->first()->value;
            $app_store_link  = $join_us->where('name','app_store_rider')->first()->value;
        }

        return view('home.apps',compact('play_store_link','app_store_link'));
    }

}
