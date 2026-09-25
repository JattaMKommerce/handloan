<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Api;
use App\Models\Report;
use App\Models\Commission;
use App\Models\Aepsreport;
use App\Models\Aepsfundreport;
use App\Models\Aepsfundrequest;
use App\Models\Upiid;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use MiladRahimi\Jwt\Generator;
use MiladRahimi\Jwt\Parser;
use MiladRahimi\Jwt\Cryptography\Keys\HmacKey;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use GeoIP;
use Illuminate\Support\Facades\Http;

class CmsController extends Controller
{
    protected $api;
    public function __construct(){
        $this->api = Api::where('code', 'cmsapi')->first();
    }
     public function index(Request $post)
    {  
      
        return view('service.cms');
    }
    
    public function transaction(Request $post)
    {
          if(!$this->api || $this->api->status == 0){
                return response()->json(['statuscode' => 'ERR', 'status' => "CMS Service Currently Down.", 'message' => "CMS Service Currently Down."]);
            }
       
        if(!$post->has('user_id')){
            $post['user_id'] = \Auth::id();
        }
        
        do {
            $post['txnid'] = $this->transcode()."CMS".rand(1111111111, 9999999999);
        } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

        $url       = $this->api->url."airtelcms/V2/airtel/index";  //"finocms/fino/generate_url";//
        $ip = $post->ip();
        $response = Http::get("http://ip-api.com/json/{$ip}");
        //$gpsdata  =  GeoIP::getLocation($post->ip());
        if ($response->successful()) {
            $data = $response->json();
            $latitude = $data['lat'];
            $longitude = $data['lon'];
        
            
        }else
        {
            return response()->json(['statuscode' => 'ERR', 'status' => "CMS Service Currently Down.", 'message' => "CMS Service Currently Down."]);
        }
        $parameter = [
           'longitude' => $longitude,
           'latitude' => $latitude,
            "refid" => $post->txnid,
           
        ];

        $payload =  [
            "timestamp" => time(),
            "partnerId" => $this->api->username,
            "reqid"     => $post->user_id.time()
        ];

     //   $token = JWT::encode($payload, $this->api->password, 'HS256');
        $token = $this->getToken(\Auth::id().Carbon::now()->timestamp);
        $header = array(
            "Cache-Control: no-cache",
            "Content-Type: application/json",
            "Token: ".$token['token'],
            "Authorisedkey: ".$this->api->optional1
        );

        $query = json_encode($parameter);
                
        $results = \Myhelper::curl($url, "POST", json_encode($parameter), $header, "yes", 'Cms', $post->panNo);
        $result=trim($results['response']);
        //dd($url,$header,$parameter,$results);
         \DB::table('rp_log')->insert([
         'ServiceName' => "Airtel CMS",
         'header' => json_encode($header),
         'body' => json_encode($parameter),
         'response' => $results['response'],
          'url' => $url,
            'created_at' => date('Y-m-d H:i:s')
         ]);
      
        if($result != ""){
            $response = json_decode($result);
            if(isset($response->responsecode) && $response->responsecode == "1"){
                \DB::table('cms_orders')->insert([
                    'txnid' => $post->txnid,
                    'user_id' => $post->user_id,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                
                return response()->json([
                    'statuscode' => "TXN",
                    'message'    => "Transaction Successfull",
                    'url'        => $response->redirectionUrl
                ]);
            }
        }

        return response()->json(['statuscode' => "ERR", "message" => isset($response->message) ? $response->message : "Something went wrong"]);
    }
    
    //  public function getToken($uniqueid)
    // {
    //     $payload =  [
    //         "timestamp" => time(),
    //         "partnerId" => $this->api->username,
    //         "reqid"     => $uniqueid
    //     ];
        
    //     $key = $this->api->password;
    //     $signer = new HS256($key);
    //     $generator = new JwtGenerator($signer);
    //     return ['token' => $generator->generate($payload), 'payload' => $payload];
    // }
    
    public function getToken($uniqueid)
    {
        $payload =  [
            "timestamp" => time(),
            "partnerId" => $this->api->username,
            "reqid"     => $uniqueid
        ];
        
        //$keyString = $this->api->password;
         $keyString = $this->api->password;
        
        if (strlen($keyString) < 32) {
            throw new \Exception ("Key length is too short. It must be at least 32 characters.");
        }
        $key = new HmacKey($keyString);
    
        $algorithm = new HS256($key);
    
        // Generate a JWT
        $generator = new Generator($algorithm);
    
        try {
            $jwt = $generator->generate($payload);
           //dd($jwt);
            return ['token' => $jwt, 'payload' => $payload];
        } catch (\Exception $e) {
           
            dd($e->getMessage());
        }
         
    }
}    