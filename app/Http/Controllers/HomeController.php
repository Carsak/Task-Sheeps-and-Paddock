<?php

namespace App\Http\Controllers;

use App\Sheep;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
	// 10 овечек рандомно селяться по 4 загонам. В загоне должно быть мин 1 овечка
	public function welcome()
	{
		$paddock = [1, 1, 1, 1];
		$count = count($paddock); // 4
		$total = 10 - $count; // 6

		// Если загоны пустые, рандомно расселяет овечек
		if ( Sheep::isPaddockEmpty() ) {
			DB::beginTransaction();

			while ( $total > 0 ) {
				// случайно выбираем, в какой загон расселить одну овечку
				$number = mt_rand(0, 3);

				$paddock[$number] += 1;
				$total -= 1;
			}

			// Запись в таблицу
			foreach ( $paddock as $key => $value ) {
				for ( $i = 1; $i <= $value; $i++ ) {
					Sheep::add($key + 1);
				}
			}

			DB::commit();
		}

		$all = DB::table('sheep')->where('active', 1)->orderBy('paddock')->get();

		$paddock = [];
		foreach ( $all as $sheep ) {
			$paddock[$sheep->paddock][] = $sheep->id;
		}

		return view('welcome', ['paddock' => $paddock, 'all' => $all]);
	}

	public function reproduce()
	{
		$padList    = [1, 2, 3, 4];
		$randomList = [];
		foreach ( $padList as $paddock ) {
			$total = DB::table('sheep')->where([['paddock', '=', $paddock], ['active', '=', '1']])->count();

			if ( $total >= Sheep::NumberToReproduce ) {
				$randomList[] = $paddock;
			}
		}

		$count = count($randomList);
		$msg   = json_encode(0);

		if ( $count > 0 ) {
			$index = (mt_rand(Sheep::MinPaddock, count($randomList)) - 1);

			$id  = Sheep::add($randomList[$index]);
			$msg = json_encode(['paddock' => $randomList[$index], 'sheep_id' => $id]);
		}

		echo $msg;
	}

	public function sleep()
	{
		$id    = Sheep::sleepOne();
		$moved = Sheep::checkPaddock();

		$msg = ['sleep' => ['id' => $id], 'moved' => $moved];

		echo json_encode($msg);
	}

	public function stat()
	{
		$all   = DB::table('sheep')->latest()->count();
		$live  = DB::table('sheep')->where('active', 1)->latest()->count();
		$sleep = DB::table('sheep')->where('active', 0)->latest()->count();

		$min = DB::table('sheep')->select('paddock', DB::raw('COUNT(id) as total'))->groupBy('paddock')->orderBy('total')->first();
		$max = DB::table('sheep')->select('paddock', DB::raw('COUNT(id) as total'))->groupBy('paddock')->orderBy('total', 'desc')->first();

		return view('stat', ['all' => $all, 'live' => $live, 'sleep' => $sleep, 'min' => $min, 'max' => $max]);
	}
}