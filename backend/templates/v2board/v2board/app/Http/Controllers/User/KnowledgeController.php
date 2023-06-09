<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Utils\Helper;
use Illuminate\Http\Request;
use App\Models\Knowledge;

class KnowledgeController extends Controller
{
    public function fetch(Request $request)
    {
        if ($request->input('id')) {
            $knowledge = Knowledge::where('id', $request->input('id'))
                ->where('show', 1)
                ->first()
                ->toArray();
            if (!$knowledge) abort(500, __('Article does not exist'));
            $user = User::find($request->user['id']);
            $userService = new UserService();
            if (!$userService->isAvailable($user)) {
                $this->formatAccessData($knowledge['body']);
            }
            $subscribeUrl = Helper::getSubscribeUrl("/api/v1/client/subscribe?token={$user['token']}");
            $knowledge['body'] = str_replace('{{siteName}}', config('v2board.app_name', 'V2Board'), $knowledge['body']);
            $knowledge['body'] = str_replace('{{subscribeUrl}}', $subscribeUrl, $knowledge['body']);
            $knowledge['body'] = str_replace('{{urlEncodeSubscribeUrl}}', urlencode($subscribeUrl), $knowledge['body']);
            $knowledge['body'] = str_replace(
                '{{safeBase64SubscribeUrl}}',
                str_replace(
                    array('+', '/', '='),
                    array('-', '_', ''),
                    base64_encode($subscribeUrl)
                ),
                $knowledge['body']
            );
            $this->apple($knowledge['body']);
            return response([
                'data' => $knowledge
            ]);
        }
        $builder = Knowledge::select(['id', 'category', 'title', 'updated_at'])
            ->where('language', $request->input('language'))
            ->where('show', 1)
            ->orderBy('sort', 'ASC');
        $keyword = $request->input('keyword');
        if ($keyword) {
            $builder = $builder->where(function ($query) use ($keyword) {
                $query->where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('body', 'LIKE', "%{$keyword}%");
            });
        }

        $knowledges = $builder->get()
            ->groupBy('category');
        return response([
            'data' => $knowledges
        ]);
    }

    private function formatAccessData(&$body)
    {
        function getBetween($input, $start, $end){$substr = substr($input, strlen($start)+strpos($input, $start),(strlen($input) - strpos($input, $end))*(-1));return $start . $substr . $end;}
        while (strpos($body, '<!--access start-->') !== false) {
            $accessData = getBetween($body, '<!--access start-->', '<!--access end-->');
            if ($accessData) {
                $body = str_replace($accessData, '<div class="v2board-no-access">'. __('You must have a valid subscription to view content in this area') .'</div>', $body);
            }
        }
    }
    private function apple(&$body)
    {
		try{
            // Phối hợp với https://github.com/Github-Aiko/autoappleid sử dụng
            // Nếu không có mật khẩu trang chia sẻ, xin hãy để trống
            // Các biến front-end {{apple_idX}} {{apple_pwX}} {{apple_statusX}} {{apple_timeX}} X là số thứ tự bắt đầu từ 0
            $req = json_decode($this->api_request_curl("https://xxx.xxx/api/share.php?share_link=xxxx&password=xxxx"), true);
            $accounts = $req["accounts"];
            for ($i=0;$i<sizeof($accounts);$i++) {
                $body = str_replace("{{apple_id$i}}", $accounts[$i]["username"], $body);
                $body = str_replace("{{apple_pw$i}}", $accounts[$i]["password"], $body);
                $body = str_replace("{{apple_status$i}}", $accounts[$i]["status"]?"正常":"异常", $body);
                $body = str_replace("{{apple_time$i}}", $accounts[$i]["last_check"], $body);
            }
		}catch (\Exception $error) {
           for ($i=0;$i<10;$i++) {
                $body = str_replace("{{apple_id$i}}", "Tìm nạp không thành công", $body);
                $body = str_replace("{{apple_pw$i}}", "Tìm nạp không thành công", $body);
                $body = str_replace("{{apple_status$i}}", "Tìm nạp không thành công", $body);
                $body = str_replace("{{apple_time$i}}", "Tìm nạp không thành công", $body);
           }
        }
    }
    
    private function api_request_curl($url) {
        if (empty($url)) return '';
        
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL, $url);
        curl_setopt($curl,CURLOPT_TIMEOUT, 30);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json, text/plain, */*'
        ));
    
        $result = curl_exec($curl);
        if($result === false){
            throw new Exception('Http request message :'.curl_error($curl));
        }
    
        curl_close($curl);
        return $result;
    }
}
