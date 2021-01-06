<?php

namespace App\Http\Controllers;

use App\Payment\PagSeguro\CreditCard;
use App\Payment\PagSeguro\Notification;
use App\Store;
use Illuminate\Http\Request;
use Mockery\Exception;
//use Ramsey\Uuid\Uuid;
class CheckoutControler extends Controller
{
    public function index()
    {       try {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!session()->has('cart')) return redirect()->route('home');

        $this->makePagSeguroSession();

        $cartItens = array_map(function ($line) {
            return $line['amount'] * $line['price'];
        }, session()->get('cart'));

        $cartItens = array_sum($cartItens);

        return view('checkout', compact('cartItens'));

    } catch (\Exception $e){
        session()->forget('pagseguro_session_code');
        redirect()->route('checkout.index');
    }

    }

    public function oi(){

        $base10=100;
        echo $base10;
        /*$valor = '';
        $ite = 64;

        for($i=0;$i<7;$i++){
            $aux = $base10-$ite;
            if ($aux>0){
                $base10 = $base10-$ite;
                $valor = '1'.$valor;
            }else{
                $valor = '0'.$valor;
            }
            $ite = $ite/2;
        }
        print($valor);*/

       // $numbers = [1, 3, 10, 4, 5, 6, 67, 2,8,4,4,4,4,4,7,8,8,3,3,10];
        /*$vet = array();

        $valor=0;
        for($i=0;$i<sizeof($numbers);$i++){
            if(in_array($numbers[$i],$vet)){
                $valor++;
            }else{
                array_push($vet,$numbers[$i]);
            }
        }*/
        //$v = sizeof($numbers);
        //$r='';
        //for($i=0;$i<$v;$i++){
          //  if($numbers[$i]%2==0){
            //  $r .=  $numbers[$i] . '</br>';
            //}
        //}
        //print($r);

    }
    public function proccess(Request $request)
    {
        try{
            $dataPost = $request->all();
            $user = auth()->user();
            $cartItens = session()->get('cart');
            $stores = array_unique(array_column($cartItens,'store_id'));
            $reference = 'XPTO';//Uuid::uuid4();
            $creditCardPayment = new CreditCard($cartItens,$user,$dataPost,$reference);
            $result = $creditCardPayment->doPayment();


            $userOrder = [
                'reference' => $reference,
                'pagseguro_code' => 1,//$result->getCode(),
                'pagseguro_status' => 1,//$result->getStatus(),
                'items' => serialize($cartItens)
            ];

            $userOrder = $user->orders()->create($userOrder);
                $userOrder->stores()->sync($stores);

                //notificar lojas ao fazer pedido
            $store = (new Store())->notifyStoreOwners($stores);

            session()->forget('cart');
            session()->forget('pagseguro_session_code');
            return response()->json([
                'data' => [
                    'status' => true,
                    'message' => 'Pedido efetuado com sucesso',
                    'order' => $reference
                ]
            ]);
        }catch (\Exception $e ){
            $message = env('APP_DEBUG') ? singlexml_load_string($e->getMessage()) : 'Pedido não pode ser efetuado ';
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => $message
                ]
            ],401);
        }
    }
    public function thanks()
    {
        return view('thanks');
    }
    public function notification()
    {
        try{
            $notification = new Notification();
            $notification = $notification->getTransaction();

            $reference = base64_decode($notification->getReference());

            $userOrder = UserOrder::whereReference($reference);
            $userOrder->update([
                'pagseguro_status' => $notification->getStatus()
            ]);

            if($notification->getStatus() == 3) {
                // Liberar o pedido do usuário..., atualizar o status do pedido para em separação
                //Notificar o usuário que o pedido foi pago...
                //Notificar a loja da confirmação do pedido...
            }

            return response()->json([], 204);

        } catch (\Exception $e) {
            $message = env('APP_DEBUG') ? $e->getMessage() : '';

            return response()->json(['error' => $message], 500);
        }
    }

    private function makePagSeguroSession()
    {
        if(!session()->has('pagseguro_session_code')){

            $sessionCode = \PagSeguro\Services\Session::create(
                \PagSeguro\Configuration\Configure::getAccountCredentials()
            );

            return session()->put('pagseguro_session_code',$sessionCode->getResult());
        }


    }
}
