<?php

namespace App;

use Illuminate\Support\Facades\DB;

class Sheep
{
	const NumberToReproduce = 2;
	const MinPaddock = 1;
	const MaxPaddock = 4;

	const ActionAdd = 'add';
	const ActionSleep = 'sleep';

	public static function add($paddock)
	{
		$id = false;
		if ( abs($paddock) > 0 ) {
			$id = DB::table('sheep')->insertGetId(['paddock' => $paddock]);

			Log::write(Sheep::ActionAdd);
		}

		return $id;
	}

	public static function isPaddockEmpty()
	{
		$sheep = DB::table('sheep')->latest()->first();

		return empty($sheep->id);
	}

	public static function reset()
	{
		DB::table('sheep')->truncate();
		DB::table('history')->truncate();
	}

	// Они просто спят
	public static function sleepOne($paddock = false)
	{
		if ( abs($paddock) > 0 ) {
			$sheep = DB::table('sheep')->where([['paddock', '=', $paddock], ['active', '=', '1']])->first();
		} else {
			$sheep = DB::table('sheep')
				->select('id', DB::raw('COUNT(id) as my'))
				->where('active', '1')
				->groupBy('paddock')
				->havingRaw('COUNT(id) > 1')->inRandomOrder()->first();
		}

		if ( !empty($sheep->id) ) {
			DB::table('sheep')->where('id', $sheep->id)->update(['active' => 0]);
			Log::write(Sheep::ActionSleep);
		}

		return empty($sheep->id) ? 0 : $sheep->id;

	}

	// Если в каком либо загоне осталось меньше одной, добавляем из самой населенной
	public static function checkPaddock()
	{
		$padList = [];

		for ( $i = Sheep::MinPaddock; $i <= Sheep::MaxPaddock; $i++ ) {
			$padList[$i] = DB::table('sheep')->where([['paddock', '=', $i], ['active', '=', '1']])->count();
		}

		$max = array_search(max($padList), $padList);
		$min = array_search(min($padList), $padList);

		$total = min($padList);

		if ( $min != $max && $total === 1 ) {
			$id  = Sheep::move($max, $min);
			$msg = ['id' => $id, 'from' => $max, 'to' => $min];
		} else {
			$msg = ['none'];
		}

		return $msg;
	}

	public static function move($from, $to)
	{
		$sheep = DB::table('sheep')->where([['paddock', '=', $from], ['active', '=', '1']])->latest()->first();

		DB::table('sheep')->where('id', $sheep->id)->update(['paddock' => $to]);

		return $sheep->id;
	}
}
