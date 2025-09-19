<?php 
	namespace App\Services;
	use App\Models\ShippingCompany;
	use App\Models\Role;
	
	class MasterService
	{ 
		public function getShippingCompanies($status = null) 
		{
			$query = ShippingCompany::query();
			if($status)
			{
				$query->where('status', $status);
			}
			return $query->get();
		}
		
		public function getShippingCompaniesById($shippingCompanyId = null) 
		{
			$query = ShippingCompany::where('id', $shippingCompanyId)->where('status', 1); 
			return $query->first();
		}
		
		public function getRoles($status = null) 
		{
			$query = Role::query();
			if($status)
			{
				$query->where('status', $status);
			}
			return $query->get();
		}
	}