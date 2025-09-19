<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Vendor;
use App\Models\VendorAddress;
use App\Models\OrderActivity;
use App\Models\OrderStatus;
use App\Models\Billing;
use App\Models\Packaging;
use App\Models\WeightFreeze;
use App\Models\Customer;
use App\Models\User;
use App\Models\CustomerAddress;
use App\Models\PincodeService;
use App\Models\ShippingCompany;
use App\Models\CourierWarehouse;
use App\Models\ProductCategory; 
use DB,Auth,File,Helper,Excel;
use Illuminate\Support\Facades\Http;
class ApiController extends Controller

{
}
?>