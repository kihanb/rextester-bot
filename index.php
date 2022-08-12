<?php 

define('API_KEY','YOUR-BOT-TOKEN');
function Bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    curl_close($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
       return $res;
    }
}

function run ($lang,$code){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://rextester.com/rundotnet/Run');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "LanguageChoiceWrapper=$lang&Program=".urlencode($code));
    $data = json_decode(curl_exec($ch),true);
    curl_close($ch);
    return $data;
}

$update = json_decode(file_get_contents('php://input'));
if(isset($update->message)){
    $message = $update->message; 
    $chat_id = $message->chat->id;
    $textmessage = $message->text;
}else
die();

$langs = ['php'=>8,'asm'=>15,'bash'=>38,'csharp'=>1,'cppgcc'=>7,'cgcc'=>6,'cclang'=>26,'clojure'=>47,'commonlisp'=>18,'d'=>30,'erlang'=>40,'fsharp'=>3,'go'=>20,'python'=>5,'perl'=>13];
$langs_names = ['php','asm','bash','csharp','cppgcc','cgcc','cclang','clojure','commonlisp','d','erlang','fsharp','go','python','perl'];

if($textmessage == '/start'){

	bot('sendMessage',['chat_id'=>$chat_id,'text'=>"Simple But Very Lite Bot to Test Your Code!\n\nCoded By @kihanb",]);

}elseif(($lang = array_search(str_replace('/','',$textmessage),$langs_names)) !== false){

	bot('sendMessage',['chat_id'=>$chat_id,'reply_markup'=>json_encode(['force_reply'=>true]),'text'=>"Ok, give me some ".$langs_names[$lang]." code to execute",]);

}elseif(isset($textmessage) && isset($message->reply_to_message->text) && preg_match('/Ok, give me some (.*?) code to execute/', $message->reply_to_message->text, $type)){
    
    if($langs[$type[1]] == 8)
    $code = '<?php ' . str_replace(['<?php','?>'],['',''],$textmessage) . '?>';
    else
    $code = $textmessage;
    
    $run = run($langs[$type[1]],$code);
    $Result = $run['Result'];
    $Errors = $run['Errors'];
    $Stats = $run['Stats'];
    bot('sendMessage',['chat_id'=>$chat_id,'parse_mode'=>"Markdown",'text'=>"*Result:*\n``` $Result ```\n\n*Errors:*\n``` $Errors ```\n\n*Stats:*\n``` $Stats ```",]);
}
if($textmessage == '/setMyCommands'){
    foreach($langs_names as $name)
    @$array[] = ['command'=>$name,'description'=>"execute $name"];
    $dd = bot('setMyCommands',['commands'=>json_encode($array)]);
    bot('sendMessage',['chat_id'=>$chat_id,'text'=>"$dd"]);
}
?>
