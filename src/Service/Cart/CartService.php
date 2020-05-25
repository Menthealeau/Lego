<?php


namespace App\Service\Cart;


use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductsRepository;

class CartService
{
    protected $session;
    protected $productrepo;


    public function __construct(SessionInterface $session, ProductsRepository $productrepo)
    {
        $this->session = $session;
        $this->productrepo = $productrepo;

    }

    public function add(int $id){

        $panier = $this->session->get('panier', []);

        if (isset($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }


        $this->session->set('panier', $panier);
    }
    public function remove(int $id){

        $panier = $this->session->get('panier', []);

        if(!empty($panier[$id] && $panier[$id].'quantity'>0))
        {
            $panier[$id]--;

            if($panier[$id].'quantity' <=0)

            {
                unset($panier[$id]);
            }

        }


        $this->session->set('panier', $panier);
    }
    public function getCart() :array {

        $panier = $this->session->get('panier', []);
        $panierRempli = [];

        foreach($panier as $id => $quantity)
        {
            $panierRempli[] = [

                'product'=> $this->productrepo->find($id),
                'quantity' => $quantity
            ];
        }

        return $panierRempli;
    }
    public function getTotal() :float {

        $total = 0;
        foreach($this->getCart() as $item)
        {
            $total +=$item['product']->getPrice() * $item['quantity'];
        }

        return $total;

    }

    public function deleteProduct(int $id) {


        $panier = $this->session->get('panier', []);
        if(!empty($panier[$id].'quantity'))
        {
            unset($panier[$id]);
        }

        $this->session->set('panier', $panier);

    }

}