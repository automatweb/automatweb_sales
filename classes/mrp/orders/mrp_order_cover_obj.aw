<?php

class mrp_order_cover_obj extends _int_object
{
	public function get_price_for_order_and_amt($order, $amt)
	{
		$cover = 0;
		switch($this->prop("cover_type"))
		{
			case 2:
				$cover += $amt * $this->prop("cover_amt");
				break;

			case 1:
				$cover += ($this->prop("cover_amt") * $order->get_total_price_for_amt($amt)) / 100.0;
				break;
			
			case 0:
			default:
				$cover += $this->prop("cover_amt");
				break;
		}
		return $cover;
	}

	public function get_price_for_order_and_amt_and_price($order, $amt, $price)
	{
		$cover = 0;
		switch($this->prop("cover_type"))
		{
			case 2:
				$cover += $amt * $this->prop("cover_amt");
				break;

			case 1:
				$cover += ($this->prop("cover_amt") * $price) / 100.0;
				break;
			
			case 0:
			default:
				$cover += $this->prop("cover_amt");
				break;
		}
		return $cover;
	}

	public function get_cover_types()
	{
		return array(
			0 => t("Kindel summa"),
			1 => t("Protsent hinnalt"),
			2 => t("Summa t&uuml;kilt")
		);
	}

	public function add_applies_general()
	{
		$this->set_prop("applies_all", 1);
		$this->save();
	}

	public function add_applies_resource($resource)
	{
		$this->connect(array("to" => $resource->id(), "reltype" => "RELTYPE_APPLIES_RESOURCE"));
	}

	public function add_applies_prod($prod)
	{
		$this->connect(array("to" => $prod->id(), "reltype" => "RELTYPE_APPLIES_PROD"));
	}

	public function move_to_group($gp)
	{
		$this->set_prop("belongs_group", $gp->id());
		$this->save();
	}
}

?>
