<?php
/**
 * Test FixMultipleUnclosedFormattingTags class
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

class FixMultipleUnclosedFormattingTagsTest extends TestCase {
	/**
	 * Test FixMultipleUnclosedFormattingTags::catchTemplateName()
	 */
	public function testCatchTemplateName() {
		$method = new \ReflectionMethod( '\HclearBot\FixMultipleUnclosedFormattingTags::catchTemplateName' );
		$method->setAccessible( true );

		// With template test
		$expected[1] = [
			'Template:test',
			'Template:test1',
			'Template:test2',
			'Template:test4',
			'Template:test3'
		];
		$text[1] = <<<TEXT
*{{test}}{{test1|test}}
:{{test2}}{{test4}}
::{{test3|
This is a test.
}}
TEXT;
		$result[1] = $method->invoke( new \HclearBot\FixMultipleUnclosedFormattingTags(), $text[1] );
		$this->assertEquals( $expected[1], $result[1] );

		// Without template test
		$expected[2] = [
			'Portal:物理学/box-header',
			'Portal:物理学/简介',
			'Portal:物理学/box-footer',
			'test'
		];
		$text[2] = <<<TEXT
{{/box-header|物理主題首頁|Portal:物理学/简介}}
{{Portal:物理学/简介}}
{{/box-footer|}}
		{{:test}}
TEXT;
		$result[2] = $method->invoke( new \HclearBot\FixMultipleUnclosedFormattingTags(), $text[2], 'Portal:物理学' );
		$this->assertEquals( $expected[2], $result[2] );
	}
}