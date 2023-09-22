<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Entity\Customers;
use App\Entity\Coupon;
use App\Entity\TaxRates;

class CalculatePriceController extends AbstractController
{
    #[Route('/calculate/price', name: 'app_calculate_price',methods: ['POST'])]
    public function calculatePrice(Request $request, EntityManagerInterface $entityManager)
    {
        $requestData = json_decode($request->getContent(), true);

    

        $productId = $requestData['product'];
        $product = $entityManager->getRepository(Product::class)->find($productId);

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], 400);
        }

  
        $taxNumber = $requestData['taxNumber'];
        $customer = $entityManager->getRepository(Customers::class)->findOneBy(['tax_number' => $taxNumber]);
        $taxRate = $entityManager->getRepository(TaxRates::class)->findOneBy(['country_code' => substr($taxNumber, 0, 2)]);
      
        if (!$customer) {
            return new JsonResponse(['error' => 'Customer not found'], 400);
        }


        $couponCode = $requestData['couponCode'];
        $couponDiscount = $this->getCouponDiscount($couponCode, $entityManager);


        $taxRate = $this->getTaxRate(substr($taxNumber, 0, 2), $entityManager);


        $productPrice = $product->getPrice();
        $discountedPrice = $this->applyCouponDiscount($productPrice, $couponDiscount);
        $totalPrice = $this->applyTax($discountedPrice, $taxRate);

        return new JsonResponse(['totalPrice' => $totalPrice], 200);
    }

    private function getCouponDiscount(?string $couponCode, EntityManagerInterface $entityManager)
    {
        $coupon = $entityManager->getRepository(Coupon::class)->findOneBy(['code' => $couponCode]);

        if ($coupon) {
            if ($coupon->getDiscountType() === 'percentage') {
                return $coupon->getDiscountAmount();
            } elseif ($coupon->getDiscountType() === 'fixed_amount') {
                return $coupon->getDiscountAmount();
            }
        }

        return 0;
    }

    private function getTaxRate(string $countryCode, EntityManagerInterface $entityManager)
    {
        $taxRate = $entityManager->getRepository(TaxRates::class)->findOneBy(['country_code' => $countryCode]);

        if ($taxRate) {
            return $taxRate->getRatePercentage();
        }

        return 0;
    }

    private function applyCouponDiscount(float $price, float $discount)
    {
        return $price - ($price * $discount);
    }

    private function applyTax(float $price, float $taxRate)
    {
        return $price + ($price * $taxRate / 100);
    }
    
}


