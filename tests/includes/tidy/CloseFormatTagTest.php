<?php
/**
 * Test the class that close unclosed format tags
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */

namespace HclearBot\test;

use PHPUnit\Framework\TestCase;

class CloseFormatTagTest extends TestCase {
	public function testScenario1_1() {
		$str = '<u>miss<u>ed <u>all my H<u>istory lectures.';
		$expected = '<u>missed all my H</u>istory lectures.';
		$tidy = new \HclearBot\CloseFormatTag( $str, 'u' );
		$this->assertEquals( $expected, $tidy->doClose() );
	}

	public function testScenario1_2() {
		$str = '<u>missed all my H<u>istory lectures.';
		$expected = '<u>missed all my H</u>istory lectures.';
		$tidy = new \HclearBot\CloseFormatTag( $str, 'u' );
		$this->assertEquals( $expected, $tidy->doClose() );
	}

	public function testScenario2_1() {
		$str = '<u>miss<u>ed all my H</u>istory lectures.';
		$expected = '<u>missed all my H</u>istory lectures.';
		$tidy = new \HclearBot\CloseFormatTag( $str, 'u' );
		$this->assertEquals( $expected, $tidy->doClose() );
	}

	public function testScenario2_2() {
		$str = '<u>miss<u>ed all m<u>y H</u>istory lectures.';
		$expected = '<u>miss</u>ed all m<u>y H</u>istory lectures.';
		$tidy = new \HclearBot\CloseFormatTag( $str, 'u' );
		$this->assertEquals( $expected, $tidy->doClose() );
	}

	public function testLoop() {
		$str = '<u>miss<u>ed all<u> my H</u>istory lectures. You have <u>w<u>asted a whole <u>t</u>erm.';
		$expected = '<u>miss</u>ed all<u> my H</u>istory lectures. You have <u>w</u>asted a whole <u>t</u>erm.';
		$tidy = new \HclearBot\CloseFormatTag( $str, 'u' );
		$this->assertEquals( $expected, $tidy->doClose() );
	}

	public function testLongText() {
		$str = "{{taxobox
| name = 雷東達鱷
|fossil_range = {{fossil_range|205}}[[三疊紀]]晚期
|image
|image_width=250px
|image_caption = 
|regnum = [[動物界]] Animalia
|phylum = [[脊索動物門]] Chordata
|classis = [[蜥形綱]] Sauropsida
|ordo = [[堅蜥目]] Aetosauria
|familia = [[鍬鱗龍科]] Stagonolepididae
|genus = '''雷東達鱷屬 ''Redondasuchus'''''
|genus_authority = [[阿德里安·亨特|Hunt]] and [[史賓賽·盧卡斯|Lucas]], 1991
| type_species = '''''Redondasuchus reseri''''
| type_species_authority = Hunt and Lucas, 1991
| subdivision_ranks = [[種]]
| subdivision = 
*'''''R. reseri''''' <br><small>Hunt and Lucas, 1991</small>
*'''''R. rineharti''''' <br><small>Spielmann ''et al.'', 2006<small>
}}";
		$expected = "{{taxobox
| name = 雷東達鱷
|fossil_range = {{fossil_range|205}}[[三疊紀]]晚期
|image
|image_width=250px
|image_caption = 
|regnum = [[動物界]] Animalia
|phylum = [[脊索動物門]] Chordata
|classis = [[蜥形綱]] Sauropsida
|ordo = [[堅蜥目]] Aetosauria
|familia = [[鍬鱗龍科]] Stagonolepididae
|genus = '''雷東達鱷屬 ''Redondasuchus'''''
|genus_authority = [[阿德里安·亨特|Hunt]] and [[史賓賽·盧卡斯|Lucas]], 1991
| type_species = '''''Redondasuchus reseri''''
| type_species_authority = Hunt and Lucas, 1991
| subdivision_ranks = [[種]]
| subdivision = 
*'''''R. reseri''''' <br><small>Hunt and Lucas, 1991</small>
*'''''R. rineharti''''' <br><small>Spielmann ''et al.'', 2006</small>
}}";
		$tidy = new \HclearBot\CloseFormatTag( $str, 'small' );
		$this->assertEquals( $expected, $tidy->doClose() );
	}
}