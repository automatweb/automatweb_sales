<?php

class inventory_manager
{
	public static function commit_transaction(inventory_transaction $transaction)
	{
	}

	// may later be public, for mass transaction committing
	private static function exec_commit_transaction($item, $quantity, $from, $to, $requester, $date = 0)
	{
	}
}
