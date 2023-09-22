<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PurchaseController extends AbstractController
{
    #[Route('/purchase', name: 'app_purchase', methods: ['POST'])]
    public function purchase(Request $request, ValidatorInterface $validator)
    {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data, [
            'product' => 'required|integer',
            'taxNumber' => 'required|regex(/^(DE|IT|GR|FR)/)',
            'couponCode' => 'nullable|string',
            'paymentProcessor' => 'required|string',
        ]);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $productPrice = $this->getProductPrice($data['product']);
        $taxRate = $this->getTaxRate($data['taxNumber']);
        $couponDiscount = $this->getCouponDiscount($data['couponCode']);

        $totalPrice = $productPrice + ($productPrice * $taxRate / 100) - $couponDiscount;

        $paymentProcessor = $data['paymentProcessor'];
        $paymentResult = $this->processPayment($totalPrice, $paymentProcessor);

        if ($paymentResult) {
            return $this->json(['message' => 'Payment successful'], Response::HTTP_OK);
        } else {
            return $this->json(['message' => 'Payment failed'], Response::HTTP_BAD_REQUEST);
        }
    }
    private function getProductPrice(int $productId)
    {

        $products = [
            1 => 100,
            2 => 20,
            3 => 10,
        ];

        return $products[$productId] ?? 0;
    }
    private function getTaxRate(string $taxNumber)
    {

        $taxRates = [
            'DE' => 19,
            'IT' => 22,
            'GR' => 24,
            'FR' => 20,
        ];

        $countryCode = substr($taxNumber, 0, 2);

        return $taxRates[$countryCode] ?? 0;
    }

    private function getCouponDiscount(?string $couponCode)
    {

        $coupons = [
            'D15' => 15,
        ];

        return $coupons[$couponCode] ?? 0;
    }
    private function processPayment(float $amount, string $paymentProcessor)
    {
        if ($paymentProcessor === 'paypal') {

            return true;
        } elseif ($paymentProcessor === 'stripe') {
            return true;
        }

        return false;
    }
    
}
