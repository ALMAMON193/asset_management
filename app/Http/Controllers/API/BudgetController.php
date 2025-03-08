<?php

namespace App\Http\Controllers\API;

use Exception;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Income;
use App\Models\Saving;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BudgetController extends Controller
{

    public function getIncomes(Request $request)
    {
        return $this->getEntityTotals(Income::class, $request, 'incomes');
    }

    public function getExpenses(Request $request)
    {
        return $this->getEntityTotalsForExpense(Expense::class, $request, 'expenses');
    }

    public function getSavings(Request $request)
    {
        return $this->getEntityTotals(Saving::class, $request, 'savings');
    }

    public function getTaxes(Request $request)
    {
        return $this->getEntityTotals(Tax::class, $request, 'taxes');
    }

    private function getEntityTotalsForExpense($model, $request, $entityName)
    {

        try {
            $validated = $request->validate([
                'year' => 'required|integer',
                // 'month' => 'required|string|size:3|in:jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dec',
            ]);

            // $defaultItems = [
            //     [
            //         'type' => 'Home',
            //         'name' => 'Mortgage/Rent',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "This includes your monthly rent or mortgage loan repayment amount. For mortgage payments include Principal and Interest, but put Property Tax below."
            //     ],
            //     [
            //         'type' => 'Home',
            //         'name' => 'Property Taxes',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Annual property taxes divided by 12 or based on monthly bills."
            //     ],
            //     [
            //         'type' => 'Home',
            //         'name' => 'Insurance - Home/Flood/Tenant',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "This includes home insurance, flood insurance, and tenant insurance premiums."
            //     ],
            //     [
            //         'type' => 'Home',
            //         'name' => 'Utilities - Electric/Gas/Water',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track your utility bills for electric, gas, and water usage."
            //     ],
            //     [
            //         'type' => 'Home',
            //         'name' => 'Communications - Telephone/Internet',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Includes your landline and internet subscriptions."
            //     ],
            //     [
            //         'type' => 'Home',
            //         'name' => 'Home Repairs/Improvement',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Expenses related to home maintenance and home improvement projects."
            //     ],
            //     [
            //         'type' => 'Home',
            //         'name' => 'Maintenance - Winter/Spring',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Costs for maintaining your property during specific seasons."
            //     ],
            //     [
            //         'type' => 'Home',
            //         'name' => 'Lawncare/Garden',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "This covers the costs of landscaping, lawn mowing, and gardening services."
            //     ],
            //     [
            //         'type' => 'Home',
            //         'name' => 'Alarm',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Payments for home security systems or alarm monitoring."
            //     ],
            //     [
            //         'type' => 'Home',
            //         'name' => 'Condo/Neighborhood Fees',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Monthly or annual fees for shared community amenities or services."
            //     ],
            //     [
            //         'type' => 'Home',
            //         'name' => 'Housekeeping',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Regular cleaning services for your home."
            //     ],
            //     [
            //         'type' => 'Home',
            //         'name' => 'Furniture/Supplies/Misc.',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Include average annual purchases for furniture, decor, or home supplies."
            //     ],
            //     [
            //         'type' => 'Home',
            //         'name' => 'Other',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Any other home expenses not listed here."
            //     ],
            //     [
            //         'type' => 'Transport',
            //         'name' => 'Auto Payment(s)',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Payments for a car loan or lease agreement."
            //     ],
            //     [
            //         'type' => 'Transport',
            //         'name' => 'Auto Maintenance',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Any vehicle maintenance or repair expenses."
            //     ],
            //     [
            //         'type' => 'Transport',
            //         'name' => 'Auto Excise Tax',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Annual or quarterly tax on your vehicle based on its value."
            //     ],
            //     [
            //         'type' => 'Transport',
            //         'name' => 'Auto Registration',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Annual registration costs for your vehicle."
            //     ],
            //     [
            //         'type' => 'Transport',
            //         'name' => 'Auto Insurance',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Monthly or annual costs for insuring your car."
            //     ],
            //     [
            //         'type' => 'Transport',
            //         'name' => 'Parking',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Include parking fees or charges for parking."
            //     ],
            //     [
            //         'type' => 'Transport',
            //         'name' => 'Gas',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track monthly fuel costs for your car."
            //     ],
            //     [
            //         'type' => 'Transport',
            //         'name' => 'Bus/Rail Card Pass',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Regular or occasional public transit pass charges."
            //     ],
            //     [
            //         'type' => 'Transport',
            //         'name' => 'Uber/Taxis',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track Uber, Lyft, or taxi fares for transportation."
            //     ],
            //     [
            //         'type' => 'Transport',
            //         'name' => 'Other',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Any other transportation costs not listed."
            //     ],
            //     [
            //         'type' => 'Basic Living',
            //         'name' => 'Groceries',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Costs for food and groceries bought for home consumption."
            //     ],
            //     [
            //         'type' => 'Basic Living',
            //         'name' => 'Cell Phone',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Charges for mobile phone service."
            //     ],
            //     [
            //         'type' => 'Basic Living',
            //         'name' => 'Personal Care Items/Toiletries',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Items like shampoo, toothpaste, and other personal essentials."
            //     ],
            //     [
            //         'type' => 'Basic Living',
            //         'name' => 'Clothing',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Purchases of clothing and accessories."
            //     ],
            //     [
            //         'type' => 'Basic Living',
            //         'name' => 'Hairdresser/Nails',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Costs for haircuts, styling, and nail care."
            //     ],
            //     [
            //         'type' => 'Basic Living',
            //         'name' => 'Household Supplies',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Expenses for cleaning supplies, home maintenance items."
            //     ],
            //     [
            //         'type' => 'Basic Living',
            //         'name' => 'Dry Cleaners/Laundry',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track costs for dry cleaning or laundry services."
            //     ],
            //     [
            //         'type' => 'Basic Living',
            //         'name' => 'Subscriptions - News, Music, TV, Apps, etc.',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track costs for entertainment and media subscriptions like Netflix, Spotify, Phone Apps."
            //     ],
            //     [
            //         'type' => 'Basic Living',
            //         'name' => 'Other',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Any miscellaneous expenses related to daily living."
            //     ],
            //     [
            //         'type' => 'Discretionary',
            //         'name' => 'Travel',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Expenses related to vacations, flights, accommodation. As these are irregular, try and get a sense of the average annual total"
            //     ],
            //     [
            //         'type' => 'Discretionary',
            //         'name' => 'Dining Out',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track restaurant meals, take-out, or delivery costs."
            //     ],
            //     [
            //         'type' => 'Discretionary',
            //         'name' => 'Entertainment/Hobbies',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Expenses for movies, hobbies, or other entertainment activities."
            //     ],
            //     [
            //         'type' => 'Discretionary',
            //         'name' => 'Shopping/Amazon',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track shopping purchases. This is commonly one of the harder items to get right. One tip is to break things down between online and in-person."
            //     ],
            //     [
            //         'type' => 'Discretionary',
            //         'name' => 'Fitness/Health Club/Personal Trainer',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Gym membership or personal trainer fees."
            //     ],
            //     [
            //         'type' => 'Discretionary',
            //         'name' => 'Sports',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Expenses for sporting events, gear, or memberships."
            //     ],
            //     [
            //         'type' => 'Discretionary',
            //         'name' => 'Country Club Fees',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track costs for country club or similar memberships."
            //     ],
            //     [
            //         'type' => 'Discretionary',
            //         'name' => 'Boat Expense',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Any expenses related to owning or maintaining a boat."
            //     ],
            //     [
            //         'type' => 'Discretionary',
            //         'name' => 'Charity',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track charitable donations made throughout the year."
            //     ],
            //     [
            //         'type' => 'Discretionary',
            //         'name' => 'Political Contributions',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track any political donations."
            //     ],
            //     [
            //         'type' => 'Discretionary',
            //         'name' => 'Gifts',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Costs for gifts given to others."
            //     ],
            //     [
            //         'type' => 'Discretionary',
            //         'name' => 'Other',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Any other non-essential spending."
            //     ],
            //     [
            //         'type' => 'Medical',
            //         'name' => 'Health Insurance Premium',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Your health insurance premiums. An HR representative may help you with this, or a recent paystub will have a percentage of your total annual deductions."
            //     ],
            //     [
            //         'type' => 'Medical',
            //         'name' => 'Dental Insurance Premium',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Your dental insurance premium payments. An HR representative may help you with this, or a recent paystub will have a percentage of your total annual deductions."
            //     ],
            //     [
            //         'type' => 'Medical',
            //         'name' => 'Vision Insurance Premium',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Your vision insurance premiums. An HR representative may help you with this, or a recent paystub will have a percentage of your total annual deductions."
            //     ],
            //     [
            //         'type' => 'Medical',
            //         'name' => 'Co-pays/Deductibles',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Payments for doctor visits and deductibles under your insurance plan."
            //     ],
            //     [
            //         'type' => 'Medical',
            //         'name' => 'Prescriptions (net of insurance)',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track costs for prescription medications after insurance."
            //     ],
            //     [
            //         'type' => 'Medical',
            //         'name' => 'Rehab/Therapy',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Costs for rehabilitation or therapy sessions."
            //     ],
            //     [
            //         'type' => 'Medical',
            //         'name' => 'Other Medical Visits',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Costs for medical visits not included in the categories above."
            //     ],
            //     [
            //         'type' => 'Medical',
            //         'name' => 'Other',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Any other healthcare-related expenses."
            //     ],
            //     [
            //         'type' => 'Professional Fees',
            //         'name' => 'Tax Preparation',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Fees paid for preparing and filing taxes."
            //     ],
            //     [
            //         'type' => 'Professional Fees',
            //         'name' => 'Legal',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Expenses for legal services, consultations, or fees."
            //     ],
            //     [
            //         'type' => 'Professional Fees',
            //         'name' => 'Financial Planning',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track costs for financial planning and advice. You may want to distinguish if this a cost you pay out of pocket vs directly from investments"
            //     ],
            //     [
            //         'type' => 'Professional Fees',
            //         'name' => 'Other',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Any other fees for professional services not listed."
            //     ],
            //     [
            //         'type' => 'Insurance',
            //         'name' => 'Life Insurance',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track your life insurance premiums."
            //     ],
            //     [
            //         'type' => 'Insurance',
            //         'name' => 'Disability Insurance',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track premiums for disability insurance."
            //     ],
            //     [
            //         'type' => 'Insurance',
            //         'name' => 'Umbrella Insurance',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Premiums for umbrella insurance, which provides extra liability coverage."
            //     ],
            //     [
            //         'type' => 'Insurance',
            //         'name' => 'Jewelry Insurance',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track the cost of insuring valuable jewelry."
            //     ],
            //     [
            //         'type' => 'Insurance',
            //         'name' => 'Other',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Other types of insurance not listed."
            //     ],
            //     [
            //         'type' => 'Kids',
            //         'name' => 'Daycare/Preschool',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Costs for daycare, preschool, or early childhood education programs."
            //     ],
            //     [
            //         'type' => 'Kids',
            //         'name' => 'After School Fees',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Expenses for after-school care, programs, or clubs for your child."
            //     ],
            //     [
            //         'type' => 'Kids',
            //         'name' => 'Babysitting/Nanny',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Costs for hiring a babysitter or nanny, including part-time or full-time care."
            //     ],
            //     [
            //         'type' => 'Kids',
            //         'name' => 'Camps/Summer Programs',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Expenses for summer camps or other seasonal programs like sports or arts camps."
            //     ],
            //     [
            //         'type' => 'Kids',
            //         'name' => 'Transport/Travel',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Travel or transport costs related to getting your child to and from school, camps, or events."
            //     ],
            //     [
            //         'type' => 'Kids',
            //         'name' => 'Toys/General Spending',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Expenses for toys, books, or other general spending for your child."
            //     ],
            //     [
            //         'type' => 'Kids',
            //         'name' => 'Private School Fees',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Tuition fees for private schools, including any registration or enrollment fees."
            //     ],
            //     [
            //         'type' => 'Kids',
            //         'name' => 'Activities - Sports, Music, After School',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Costs for extracurricular activities like sports, music lessons, and after-school programs."
            //     ],
            //     [
            //         'type' => 'Kids',
            //         'name' => 'Food (if not included above)',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Any food expenses for your child that are not covered in the grocery section above."
            //     ],
            //     [
            //         'type' => 'Kids',
            //         'name' => 'Clothing (if not included above)',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Clothing expenses for your child that are not included in the general clothing category."
            //     ],
            //     [
            //         'type' => 'Kids',
            //         'name' => 'Healthcare/Prescriptions (if not included above)',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Medical expenses, including prescriptions, co-pays, or healthcare needs specific to your child."
            //     ],
            //     [
            //         'type' => 'Kids',
            //         'name' => 'Other',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Any other expenses for your child not covered by the categories above."
            //     ],
            //     [
            //         'type' => 'Pet',
            //         'name' => 'Veterinarian',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Expenses for veterinary visits, checkups, treatments, and emergencies for your pet."
            //     ],
            //     [
            //         'type' => 'Pet',
            //         'name' => 'Food/Prescriptions',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Costs for pet food and prescription medications for your pet."
            //     ],
            //     [
            //         'type' => 'Pet',
            //         'name' => 'Boarding Charges',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Expenses for boarding your pet when you are traveling or need temporary care."
            //     ],
            //     [
            //         'type' => 'Pet',
            //         'name' => 'Dog Walker',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Costs for dog walking services, either on a regular or occasional basis."
            //     ],
            //     [
            //         'type' => 'Pet',
            //         'name' => 'Dog Grooming',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Expenses for grooming services such as bathing, cutting, or trimming your pet's fur."
            //     ],
            //     [
            //         'type' => 'Pet',
            //         'name' => 'Other',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Any other pet-related costs, such as accessories, training, or pet insurance."
            //     ],
            //     [
            //         'type' => 'Debt Repayments',
            //         'name' => 'Student Loan Debt',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Payments toward student loan debt, including federal or private loans."
            //     ],
            //     [
            //         'type' => 'Debt Repayments',
            //         'name' => 'Credit Card Balance (if not paid off monthly)',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Track any credit card balances that are carried over month-to-month, including interest payments."
            //     ],
            //     [
            //         'type' => 'Debt Repayments',
            //         'name' => 'Home Equity Line of Credit',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Payments for a home equity line of credit (HELOC), which is a revolving loan secured by your home."
            //     ],
            //     [
            //         'type' => 'Debt Repayments',
            //         'name' => 'Other Debt',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "Any other debt repayments not covered above, including personal loans, payday loans, or medical debt."
            //     ],
            //     [
            //         'type' => 'User Defined Other',
            //         'name' => 'User Defined Other',
            //         'monthly_amount' => 0,
            //         'annual_amount' => 0,
            //         'percentage_total' => 0,
            //         'info' => "This category allows for any other specific expenses or savings you want to track that don't fit into other categories."
            //     ],
            // ];
            
            $defaultItems = [
                [
                    'type' => 'Home',
                    'name' => 'Home General',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Any other home expenses not listed here."
                ],
                [
                    'type' => 'Home',
                    'name' => 'Mortgage/Rent',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "This includes your monthly rent or mortgage loan repayment amount. For mortgage payments include Principal and Interest, but put Property Tax below."
                ],
                [
                    'type' => 'Home',
                    'name' => 'Property Taxes',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Annual property taxes divided by 12 or based on monthly bills."
                ],
                [
                    'type' => 'Home',
                    'name' => 'Insurance - Home/Flood/Tenant',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "This includes home insurance, flood insurance, and tenant insurance premiums."
                ],
                [
                    'type' => 'Home',
                    'name' => 'Utilities - Electric/Gas/Water',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track your utility bills for electric, gas, and water usage."
                ],
                [
                    'type' => 'Home',
                    'name' => 'Communications - Telephone/Internet',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Includes your landline and internet subscriptions."
                ],
                [
                    'type' => 'Home',
                    'name' => 'Home Repairs/Improvement',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Expenses related to home maintenance and home improvement projects."
                ],
                [
                    'type' => 'Home',
                    'name' => 'Maintenance - Winter/Spring',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Costs for maintaining your property during specific seasons."
                ],
                [
                    'type' => 'Home',
                    'name' => 'Lawncare/Garden',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "This covers the costs of landscaping, lawn mowing, and gardening services."
                ],
                [
                    'type' => 'Home',
                    'name' => 'Alarm',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Payments for home security systems or alarm monitoring."
                ],
                [
                    'type' => 'Home',
                    'name' => 'Condo/Neighborhood Fees',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Monthly or annual fees for shared community amenities or services."
                ],
                [
                    'type' => 'Home',
                    'name' => 'Housekeeping',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Regular cleaning services for your home."
                ],
                [
                    'type' => 'Home',
                    'name' => 'Furniture/Supplies/Misc.',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Include average annual purchases for furniture, decor, or home supplies."
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Transport General',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Any other transportation costs not listed."
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Auto Payment(s)',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Payments for a car loan or lease agreement."
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Auto Maintenance',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Any vehicle maintenance or repair expenses."
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Auto Excise Tax',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Annual or quarterly tax on your vehicle based on its value."
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Auto Registration',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Annual registration costs for your vehicle."
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Auto Insurance',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Monthly or annual costs for insuring your car."
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Parking',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Include parking fees or charges for parking."
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Gas',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track monthly fuel costs for your car."
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Bus/Rail Card Pass',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Regular or occasional public transit pass charges."
                ],
                [
                    'type' => 'Transport',
                    'name' => 'Uber/Taxis',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track Uber, Lyft, or taxi fares for transportation."
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Basic Living General',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Any miscellaneous expenses related to daily living."
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Groceries',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Costs for food and groceries bought for home consumption."
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Cell Phone',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Charges for mobile phone service."
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Personal Care Items/Toiletries',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Items like shampoo, toothpaste, and other personal essentials."
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Clothing',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Purchases of clothing and accessories."
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Hairdresser/Nails',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Costs for haircuts, styling, and nail care."
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Household Supplies',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Expenses for cleaning supplies, home maintenance items."
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Dry Cleaners/Laundry',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track costs for dry cleaning or laundry services."
                ],
                [
                    'type' => 'Basic Living',
                    'name' => 'Subscriptions - News, Music, TV, Apps, etc.',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track costs for entertainment and media subscriptions like Netflix, Spotify, Phone Apps."
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Discretionary General',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Any other non-essential spending."
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Travel',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Expenses related to vacations, flights, accommodation. As these are irregular, try and get a sense of the average annual total"
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Dining Out',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track restaurant meals, take-out, or delivery costs."
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Entertainment/Hobbies',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Expenses for movies, hobbies, or other entertainment activities."
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Shopping/Amazon',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track shopping purchases. This is commonly one of the harder items to get right. One tip is to break things down between online and in-person."
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Fitness/Health Club/Personal Trainer',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Gym membership or personal trainer fees."
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Sports',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Expenses for sporting events, gear, or memberships."
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Country Club Fees',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track costs for country club or similar memberships."
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Boat Expense',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Any expenses related to owning or maintaining a boat."
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Charity',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track charitable donations made throughout the year."
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Political Contributions',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track any political donations."
                ],
                [
                    'type' => 'Discretionary',
                    'name' => 'Gifts',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Costs for gifts given to others."
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Medical General',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Any other healthcare-related expenses."
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Health Insurance Premium',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Your health insurance premiums. An HR representative may help you with this, or a recent paystub will have a percentage of your total annual deductions."
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Dental Insurance Premium',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Your dental insurance premium payments. An HR representative may help you with this, or a recent paystub will have a percentage of your total annual deductions."
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Vision Insurance Premium',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Your vision insurance premiums. An HR representative may help you with this, or a recent paystub will have a percentage of your total annual deductions."
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Co-pays/Deductibles',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Payments for doctor visits and deductibles under your insurance plan."
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Prescriptions (net of insurance)',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track costs for prescription medications after insurance."
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Rehab/Therapy',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Costs for rehabilitation or therapy sessions."
                ],
                [
                    'type' => 'Medical',
                    'name' => 'Other Medical Visits',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Costs for medical visits not included in the categories above."
                ],
                [
                    'type' => 'Professional Fees',
                    'name' => 'Professional Fees General',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Any other fees for professional services not listed."
                ],
                [
                    'type' => 'Professional Fees',
                    'name' => 'Tax Preparation',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Fees paid for preparing and filing taxes."
                ],
                [
                    'type' => 'Professional Fees',
                    'name' => 'Legal',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Expenses for legal services, consultations, or fees."
                ],
                [
                    'type' => 'Professional Fees',
                    'name' => 'Financial Planning',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track costs for financial planning and advice. You may want to distinguish if this a cost you pay out of pocket vs directly from investments"
                ],
                [
                    'type' => 'Insurance',
                    'name' => 'Insurance General',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Other types of insurance not listed."
                ],
                [
                    'type' => 'Insurance',
                    'name' => 'Life Insurance',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track your life insurance premiums."
                ],
                [
                    'type' => 'Insurance',
                    'name' => 'Disability Insurance',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track premiums for disability insurance."
                ],
                [
                    'type' => 'Insurance',
                    'name' => 'Umbrella Insurance',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Premiums for umbrella insurance, which provides extra liability coverage."
                ],
                [
                    'type' => 'Insurance',
                    'name' => 'Jewelry Insurance',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track the cost of insuring valuable jewelry."
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Kids General',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Any other expenses for your child not covered by the categories above."
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Daycare/Preschool',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Costs for daycare, preschool, or early childhood education programs."
                ],
                [
                    'type' => 'Kids',
                    'name' => 'After School Fees',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Expenses for after-school care, programs, or clubs for your child."
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Babysitting/Nanny',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Costs for hiring a babysitter or nanny, including part-time or full-time care."
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Camps/Summer Programs',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Expenses for summer camps or other seasonal programs like sports or arts camps."
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Transport/Travel',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Travel or transport costs related to getting your child to and from school, camps, or events."
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Toys/General Spending',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Expenses for toys, books, or other general spending for your child."
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Private School Fees',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Tuition fees for private schools, including any registration or enrollment fees."
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Activities - Sports, Music, After School',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Costs for extracurricular activities like sports, music lessons, and after-school programs."
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Food (if not included above)',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Any food expenses for your child that are not covered in the grocery section above."
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Clothing (if not included above)',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Clothing expenses for your child that are not included in the general clothing category."
                ],
                [
                    'type' => 'Kids',
                    'name' => 'Healthcare/Prescriptions (if not included above)',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Medical expenses, including prescriptions, co-pays, or healthcare needs specific to your child."
                ],
                [
                    'type' => 'Pet',
                    'name' => 'Pet General',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Any other pet-related costs, such as accessories, training, or pet insurance."
                ],
                [
                    'type' => 'Pet',
                    'name' => 'Veterinarian',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Expenses for veterinary visits, checkups, treatments, and emergencies for your pet."
                ],
                [
                    'type' => 'Pet',
                    'name' => 'Food/Prescriptions',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Costs for pet food and prescription medications for your pet."
                ],
                [
                    'type' => 'Pet',
                    'name' => 'Boarding Charges',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Expenses for boarding your pet when you are traveling or need temporary care."
                ],
                [
                    'type' => 'Pet',
                    'name' => 'Dog Walker',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Costs for dog walking services, either on a regular or occasional basis."
                ],
                [
                    'type' => 'Pet',
                    'name' => 'Dog Grooming',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Expenses for grooming services such as bathing, cutting, or trimming your pet's fur."
                ],
                [
                    'type' => 'Debt Repayments',
                    'name' => 'Debt Repayments General',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Any other debt repayments not covered above, including personal loans, payday loans, or medical debt."
                ],
                [
                    'type' => 'Debt Repayments',
                    'name' => 'Student Loan Debt',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Payments toward student loan debt, including federal or private loans."
                ],
                [
                    'type' => 'Debt Repayments',
                    'name' => 'Credit Card Balance (if not paid off monthly)',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Track any credit card balances that are carried over month-to-month, including interest payments."
                ],
                [
                    'type' => 'Debt Repayments',
                    'name' => 'Home Equity Line of Credit',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "Payments for a home equity line of credit (HELOC), which is a revolving loan secured by your home."
                ],
                [
                    'type' => 'User Defined Other',
                    'name' => 'User Defined Other',
                    'monthly_amount' => 0,
                    'annual_amount' => 0,
                    'percentage_total' => 0,
                    'info' => "This category allows for any other specific expenses or savings you want to track that don't fit into other categories."
                ],
            ];

            // Fetch records from the database
            $records = $model::where('user_id', auth()->user()->id)
                ->where('year', $validated['year'])
                // ->where('month', $validated['month'])
                ->select('type', 'name', DB::raw('round(monthly_amount) as monthly_amount'), DB::raw('round(annual_amount) as annual_amount'), 'percentage_total')
                ->get()
                ->toArray();

                // Merge defaults with existing records
                $mergedRecords = collect($defaultItems)->map(function ($defaultItem) use ($records) {
                $existingRecord = collect($records)->where('type', $defaultItem['type'])->firstWhere('name', $defaultItem['name']);

                // Ensure 'info' key is retained
                return $existingRecord ? array_merge($existingRecord, ['info' => $defaultItem['info']]) : $defaultItem;
            });

            // Fetch totals and subtotals
            $totals = $this->getTotalsByModel($model, $validated);
            $subTotals = $this->getTotalsByType($model, $validated);

            return response()->json([
                'status' => true,
                'message' => "Successfully fetched $entityName data",
                'code' => 200,
                'data' => [
                    'totals' => $totals,
                    'subtotals' => $subTotals,
                    'records' => $mergedRecords->groupBy('type'),
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generic method to fetch totals for a specific entity.
     */
    private function getEntityTotals($model, Request $request, $entityName)
    {
        try {
            $validated = $request->validate([
                'year' => 'required|integer',
                // 'month' => 'required|string|size:3|in:jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dec',
            ]);

            $defaultItems = match ($model) {
                Income::class => [
                    [
                        'type' => 'Income (1)',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                        'info' => "Insert income here"
                    ],
                    [
                        'type' => 'Bonus (1)',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                        'info' => "Insert income here"
                    ],
                    [
                        'type' => 'Income (2)',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                        'info' => "Insert income here"
                    ],
                    [
                        'type' => 'Bonus (2)',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                        'info' => "Insert income here"
                    ],
                    [
                        'type' => 'Other Income (1)',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                        'info' => "Insert income here"
                    ],
                    [
                        'type' => 'Other Income (2)',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                        'info' => "Insert income here"
                    ],
                ],
                Saving::class => [
                    [
                        'type' => '401k Contribution',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                        'info' => "Track contributions made to your employer-sponsored 401k retirement plan."
                    ],
                    [
                        'type' => 'Education/529 Plan Contribution',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                        'info' => "Contributions to a 529 plan for your child's education expenses."
                    ],
                    [
                        'type' => 'Individual IRA/Roth IRA Contribution',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                        'info' => "Contributions to an Individual Retirement Account (IRA) or Roth IRA for your retirement savings."
                    ],
                    [
                        'type' => 'HSA Contribution',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                        'info' => "Contributions to a Health Savings Account (HSA), which can be used for medical expenses."
                    ],
                    [
                        'type' => 'Brokerage Contributions',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                        'info' => "Contributions to a brokerage account for general investment purposes."
                    ],
                    [
                        'type' => 'Specific Goal Based Savings',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                        'info' => "Savings dedicated to specific financial goals, such as a house, vacation, or a large purchase."
                    ],
                ],
                Tax::class => [
                    [
                        'type' => 'Federal',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                        'info' => "Track any federal taxes here."
                    ],
                    [
                        'type' => 'State',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                        'info' => "Track any state taxes here."
                    ],
                    [
                        'type' => 'Other',
                        'monthly_amount' => 0,
                        'annual_amount' => 0,
                        'percentage_total' => 0,
                        'info' => "Track any other taxes here."
                    ],
                ],
            };

            // Fetch totals
            $totals = $this->getTotalsByModel($model, $validated);

            // fetch records
            $records = $model::where('user_id', auth()->user()->id)
                ->where('year', $validated['year'])
                // ->where('month', $validated['month'])
                ->select('type', DB::raw('monthly_amount as monthly_amount'), DB::raw('round(annual_amount) as annual_amount'), 'percentage_total')
                ->get();

            // Merge defaults with existing records
            $mergedRecords = collect($defaultItems)->map(function ($defaultItem) use ($records) {
                $existingRecord = collect($records)->firstWhere('type', $defaultItem['type']);
                return $existingRecord ?? $defaultItem;
            });

            return response()->json([
                'status' => true,
                'message' => "Successfully fetched $entityName data",
                'code' => 200,
                'totals' => $totals,
                'records' => $mergedRecords,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch totals by model.
     */
  private function getTotalsByModel($model, $validated)
{
    // Fetch total values from the database
    $data = $model::where('user_id', auth()->id())
        ->where('year', $validated['year'])
        ->selectRaw('
            COALESCE(ROUND(SUM(monthly_amount)), 0) as total_monthly, 
            COALESCE(ROUND(SUM(annual_amount)), 0) as total_annual
        ')
        ->first();

    // Ensure default values to avoid null issues
    $totalMonthly = $data->total_monthly ?? 0;
    $totalAnnual = $data->total_annual ?? 0;

    // Fix percentage calculation
    $percentageTotal = ($totalAnnual > 0) ? 100 : 0; // Since this is a total model sum

    return [
        'year' => $validated['year'],
        'total_monthly' => $totalMonthly,
        'total_annual' => $totalAnnual,
        'percentage_of_total' => $percentageTotal,
    ];
}


    /**
     * Fetch totals by type.
     */
    private function getTotalsByType($model, $validated)
    {
        $data = $model::selectRaw('type, round(SUM(monthly_amount)) as total_monthly, round(SUM(annual_amount)) as total_annual, round(SUM(percentage_total)) as percentage_of_total')
            ->where('user_id', auth()->id())
            ->where('year', $validated['year'])
            // ->where('month', $validated['month'])
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        $defaultTypes = [
            'Home',
            'Transport',
            'Basic Living',
            'Discretionary',
            'Medical',
            'Professional Fees',
            'Insurance',
            'Kids',
            'Pet',
            'Debt Repayments',
            'User Defined Other',
        ];

        $result = [];
        foreach ($defaultTypes as $type) {
            $result[] = [
                'type' => $type,
                'total_monthly' => $data[$type]['total_monthly'] ?? 0,
                'total_annual' => $data[$type]['total_annual'] ?? 0,
                'percentage_of_total' => $data[$type]['percentage_of_total'] ?? 0,
            ];
        }

        return $result;

        // return $data;
    }

    public function saveIncome(Request $request)
    {
        return $this->saveRecord(Income::class, $request);
    }

    public function saveExpense(Request $request)
    {
        return $this->saveRecord(Expense::class, $request, ['name' => 'required|string']);
    }

    public function saveSaving(Request $request)
    {
        return $this->saveRecord(Saving::class, $request);
    }

    public function saveTax(Request $request)
    {
        return $this->saveRecord(Tax::class, $request);
    }

    /**
     * Generic method to save a record.
     */
    private function saveRecord($model, Request $request, array $extraRules = [])
    {
        try {
            if ($request->monthly_amount && $request->annual_amount) {
                return response()->json(['error' => 'You can only enter either monthly or annual amount'], 400);
            }

            $rules = array_merge([
                'type' => 'required|string',
                'notes' => 'nullable|string',
                'monthly_amount' => 'nullable|numeric',
                'annual_amount' => 'nullable|numeric',
                'year' => 'required|integer',
                // 'month' => 'required|string|size:3|in:jan,feb,mar,apr,may,jun,jul,aug,sep,oct,nov,dec',
            ], $extraRules);

            $validated = $request->validate($rules);

            /* $currentDate = Carbon::now();
            $validated['year'] = $currentDate->year;
            $validated['month'] = $currentDate->format('M'); */

            DB::beginTransaction();

            $record = $model::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'year' => $validated['year'],
                    // 'month' => $validated['month'],
                    'type' => $validated['type'],
                ] + (isset($validated['name']) ? ['name' => $validated['name']] : []),
                array_merge($validated, $this->calculateAmounts($validated))
            );

            $this->updatePercentages($model, $record->user_id);

            // Fetch the updated record from the database
            $freshRecord = $model::find($record->id);

            DB::commit();

            return response()->json([

                'success' => true,
                'message' => 'Record saved successfully',
                'code' => 200,
                'record' => $freshRecord
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Automatically calculate annual or monthly amounts.
     */
    private function calculateAmounts($data)
{
    $calculated = [
        'monthly_amount' => isset($data['monthly_amount']) ? number_format($data['monthly_amount'], 2, '.', '') : 0,
        'annual_amount' => isset($data['annual_amount']) ? number_format($data['annual_amount'], 2, '.', '') : 0
    ];

    if ($calculated['monthly_amount'] == 0 && $calculated['annual_amount'] > 0) {
        $calculated['monthly_amount'] = number_format($calculated['annual_amount'] / 12, 2, '.', '');
    } elseif ($calculated['annual_amount'] == 0 && $calculated['monthly_amount'] > 0) {
        $calculated['annual_amount'] = number_format($calculated['monthly_amount'] * 12, 2, '.', '');
    }

    return $calculated;
}


    /**
     * Update percentages.
     */
    private function updatePercentages($model, $userId)
    {
        $totals = $model::where('user_id', $userId)->sum('annual_amount');

        $model::where('user_id', $userId)->each(function ($record) use ($totals) {
            $record->update(['percentage_total' => $totals > 0 ? ($record->annual_amount / $totals) * 100 : 0]);
        });
    }
}
