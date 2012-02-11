<?php
/* TwitPlug
 * By Kawa
 *
 * External requirements:
 *   None!
 *
 * To use, check "Edit Profile".
 */

registerPlugin("TwitPlug");
registerSetting("twitName", "Twitter account");

function TwitPlug_Fetch()
{
	global $user, $dateformat;
	
	if(getSetting("twitName", true) != "")
	{
		$twit = strip_tags(getSetting("twitName", true));
		$feed = @file_get_contents("http://search.twitter.com/search.atom?q=from%3A".$twit."&rpp=1");
		if($feed === FALSE)
			$result = "Could not get updates for ".$twit;
		else
		{
			$feed = substr($feed, strpos($feed, "<entry>"));
			preg_match("/\<content type=\"html\"\>(.*)\<\/content\>/", $feed, $matches2);
			preg_match("/\<updated\>(.*)\<\/updated\>/", $feed, $matches3);
			preg_match("/\<twitter:source\>(.*)\<\/twitter:source\>/", $feed, $matches4);

			$content = html_entity_decode($matches2[1]);
			$updateTime = cdate($dateformat, strtotime($matches3[1]));
			$source = html_entity_decode($matches4[1]);

			$result = $content." <small>(".$updateTime.", from ".$source.")</small>";
		}

		write(
"
				<tr>
					<td class=\"cell0\">Last Tweet</td>
					<td class=\"cell1\">{0}</td>
				</tr>
", $result);
	}
}

register("profileTable", "TwitPlug_Fetch");

?>