<?php
/**
 * Bot core
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

namespace HclearBot;

use MediaWiki\OAuthClient\{
	ClientConfig,
	Consumer,
	Client,
	Token
};

class Core {
	/**
	 * Run bot
	 */
	public function run() {
		$obj = new FixMultipleUnclosedFormattingTags();
		$obj->execute();
	}

	static public function oauthAuthorize() {
		global $gConsumerKey, $gConsumerSecret, $gAccessKey, $gAccessSecret;
		if ( empty( $gConsumerKey )
				|| empty( $gConsumerSecret )
				|| empty( $gAccessKey )
				|| empty( $gAccessSecret )
		) {
			trigger_error( 'Missing configuration', E_USER_ERROR );
		}
		$endpoint = 'https://zh.wikipedia.org/w/index.php?title=Special:OAuth';
		$redir = 'https://zh.wikipedia.org/w/index.php?title=Special:OAuth?';
		$conf = new ClientConfig( $endpoint );
		$conf->setRedirURL( $redir );
		$conf->setConsumer( new Consumer( $gConsumerKey, $gConsumerSecret ) );

		$GLOBALS['gClient'] = new Client( $conf );
		$GLOBALS['gAccessToken'] = new Token( $gAccessKey, $gAccessSecret );
	}
}