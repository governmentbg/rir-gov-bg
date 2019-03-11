<?php
// Copyright (C) 2010-2017 Combodo SARL
//
//   This file is part of iTop.
//
//   iTop is free software; you can redistribute it and/or modify	
//   it under the terms of the GNU Affero General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.
//
//   iTop is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU Affero General Public License for more details.
//
//   You should have received a copy of the GNU Affero General Public License
//   along with iTop. If not, see <http://www.gnu.org/licenses/>


/**
 * Class LoginWebPage
 *
 * @copyright   Copyright (C) 2010-2017 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

require_once(APPROOT."/application/nicewebpage.class.inc.php");
require_once(APPROOT.'/application/portaldispatcher.class.inc.php');
/**
 * Web page used for displaying the login form
 */

class LoginWebPage extends NiceWebPage
{
	const EXIT_PROMPT = 0;
	const EXIT_HTTP_401 = 1;
	const EXIT_RETURN = 2;
	
	const EXIT_CODE_OK = 0;
	const EXIT_CODE_MISSINGLOGIN = 1;
	const EXIT_CODE_MISSINGPASSWORD = 2;
	const EXIT_CODE_WRONGCREDENTIALS = 3;
	const EXIT_CODE_MUSTBEADMIN = 4;
	const EXIT_CODE_PORTALUSERNOTAUTHORIZED = 5;
	const EXIT_CODE_NOTAUTHORIZED = 6;
	
	protected static $sHandlerClass = __class__;
	public static function RegisterHandler($sClass)
	{
		self::$sHandlerClass = $sClass;
	}

	public static function NewLoginWebPage()
	{
		return new self::$sHandlerClass;
	}

	protected static $m_sLoginFailedMessage = '';
	
	public function __construct($sTitle = null)
	{
	    if($sTitle === null)
        {
            $sTitle = Dict::S('UI:Login:Title');
        }

		parent::__construct($sTitle);
		$this->SetStyleSheet();
		$this->add_header("Cache-control: no-cache");
	}
	
	public function SetStyleSheet()
	{
		$this->add_linked_stylesheet(utils::GetAbsoluteUrlAppRoot().'css/login.css');
	}

	public static function SetLoginFailedMessage($sMessage)
	{
		self::$m_sLoginFailedMessage = $sMessage;
	}

	public function EnableResetPassword()
	{
		return MetaModel::GetConfig()->Get('forgot_password');
	}

	public function DisplayLoginHeader($bMainAppLogo = false)
	{
		if ($bMainAppLogo)
		{
			$sLogo = 'itop-logo.png';
			$sBrandingLogo = 'main-logo.png';
		}
		else
		{
			$sLogo = 'itop-logo-external.png';
			$sBrandingLogo = 'login-logo.png';
		}
		$sVersionShort = Dict::Format('UI:iTopVersion:Short', ITOP_APPLICATION, ITOP_VERSION);
		$sIconUrl = Utils::GetConfig()->Get('app_icon_url');
		$sDisplayIcon = utils::GetAbsoluteUrlAppRoot().'images/'.$sLogo.'?t='.utils::GetCacheBusterTimestamp();
		if (file_exists(MODULESROOT.'branding/'.$sBrandingLogo))
		{
			$sDisplayIcon = utils::GetAbsoluteUrlModulesRoot().'branding/'.$sBrandingLogo.'?t='.utils::GetCacheBusterTimestamp();
		}
		$this->add("<div id=\"login-logo\"><a href=\"".htmlentities($sIconUrl, ENT_QUOTES, 'UTF-8')."\"><img title=\"$sVersionShort\" src=\"$sDisplayIcon\"></a></div>\n");
	}

	public function DisplayLoginForm($sLoginType, $bFailedLogin = false)
	{
		switch($sLoginType)
		{
			case 'cas':
			utils::InitCASClient();					
			// force CAS authentication
			phpCAS::forceAuthentication(); // Will redirect the user and exit since the user is not yet authenticated
			break;
			
			case 'basic':
			case 'url':
			$this->add_header('WWW-Authenticate: Basic realm="'.Dict::Format('UI:iTopVersion:Short', ITOP_APPLICATION, ITOP_VERSION));
			$this->add_header('HTTP/1.0 401 Unauthorized');
			$this->add_header('Content-type: text/html; charset=iso-8859-1');
			// Note: displayed when the user will click on Cancel
			$this->add('<p><strong>'.Dict::S('UI:Login:Error:AccessRestricted').'</strong></p>');
			break;
			
			case 'external':
			case 'form':
			default: // In case the settings get messed up...
			$sAuthUser = utils::ReadParam('auth_user', '', true, 'raw_data');
			$sAuthPwd = utils::ReadParam('suggest_pwd', '', true, 'raw_data');
	
			$this->DisplayLoginHeader();
			$this->add("<div id=\"login\">\n");
			$this->add("<h1>".Dict::S('UI:Login:Welcome')."</h1>\n");
			if ($bFailedLogin)
			{
				if (self::$m_sLoginFailedMessage != '')
				{
					$this->add("<p class=\"hilite\">".self::$m_sLoginFailedMessage."</p>\n");
				}
				else
				{
					$this->add("<p class=\"hilite\">".Dict::S('UI:Login:IncorrectLoginPassword')."</p>\n");
				}
			}
			else
			{
				$this->add("<p>".Dict::S('UI:Login:IdentifyYourself')."</p>\n");
			}
			$this->add("<form method=\"post\">\n");
			$this->add("<table>\n");
			$sForgotPwd = $this->EnableResetPassword() ? $this->ForgotPwdLink() : '';
			$this->add("<tr><td style=\"text-align:right\"><label for=\"user\">".Dict::S('UI:Login:UserNamePrompt').":</label></td><td style=\"text-align:left\"><input id=\"user\" type=\"text\" name=\"auth_user\" value=\"".htmlentities($sAuthUser, ENT_QUOTES, 'UTF-8')."\" /></td></tr>\n");
			$this->add("<tr><td style=\"text-align:right\"><label for=\"pwd\">".Dict::S('UI:Login:PasswordPrompt').":</label></td><td style=\"text-align:left\"><input id=\"pwd\" type=\"password\" name=\"auth_pwd\" value=\"".htmlentities($sAuthPwd, ENT_QUOTES, 'UTF-8')."\" /></td></tr>\n");
			$this->add("<tr><td colspan=\"2\" class=\"center v-spacer\"><span class=\"btn_border\"><input type=\"submit\" value=\"".Dict::S('UI:Button:Login')."\" /></span></td></tr>\n");
			if (strlen($sForgotPwd) > 0)
			{
				$this->add("<tr><td colspan=\"2\" class=\"center v-spacer\">$sForgotPwd</td></tr>\n");
			}
			$this->add("</table>\n");
			$this->add("<input type=\"hidden\" name=\"loginop\" value=\"login\" />\n");

			$this->add_ready_script('$("#user").focus();');
						
			// Keep the OTHER parameters posted
			foreach($_POST as $sPostedKey => $postedValue)
			{
				if (!in_array($sPostedKey, array('auth_user', 'auth_pwd')))
				{
					if (is_array($postedValue))
					{
						foreach($postedValue as $sKey => $sValue)
						{
							$this->add("<input type=\"hidden\" name=\"".htmlentities($sPostedKey, ENT_QUOTES, 'UTF-8')."[".htmlentities($sKey, ENT_QUOTES, 'UTF-8')."]\" value=\"".htmlentities($sValue, ENT_QUOTES, 'UTF-8')."\" />\n");
						}
					}
					else
					{
						$this->add("<input type=\"hidden\" name=\"".htmlentities($sPostedKey, ENT_QUOTES, 'UTF-8')."\" value=\"".htmlentities($postedValue, ENT_QUOTES, 'UTF-8')."\" />\n");
					}
				}	
			}
			
			$this->add("</form>\n");
			$this->add("<form action=\"https://eauthn.egov.bg:9445/eAuthenticator/eAuthenticator.seam\" method=\"post\">\n");
			$this->add("<table>\n");
			$this->add("<tr><td colspan=\"2\" class=\"center v-spacer\"><span class=\"btn_border\"><input type=\"submit\" value=\"Вход с КЕП\" /></span></td></tr>\n");
			$this->add("</table>\n");
			$this->add("<input type=\"hidden\" name=\"SAMLRequest\" value=\"PHNhbWxwOkF1dGhuUmVxdWVzdCB4bWxuczpzYW1sPSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6YXNzZXJ0aW9uIiB4bWxuczplZ292YmdhPSJ1cm46Ymc6ZWdvdjplYXV0aDoxLjA6c2FtbDpleHQiIERlc3RpbmF0aW9uPSJodHRwczovL2VhdXRobi5lZ292LmJnOjk0NDUvZUF1dGhlbnRpY2F0b3IvZUF1dGhlbnRpY2F0b3Iuc2VhbSIgSUQ9IklUT1BfMjAxODA4MTUwMzAwMDk3OSIgSXNzdWVJbnN0YW50PSIyMDE4LTExLTA2VDE1OjI3OjA5IiBWZXJzaW9uPSIyLjAiIEFzc2VydGlvbkNvbnN1bWVyU2VydmljZVVSTD0iaHR0cHM6Ly9yaXIuZWdvdi5iZy9wYWdlcy9VSS5waHAiIEZvcmNlQXV0aG49InRydWUiIElzUGFzc2l2ZT0iZmFsc2UiIFByb3RvY29sQmluZGluZz0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOmJpbmRpbmdzOkhUVFAtUE9TVCIgUHJvdmlkZXJOYW1lPSLQoNC10LPQuNGB0YLRitGAINC90LAg0JjQmtCYINGA0LXRgdGD0YDRgdC4IiB4bWxuczpzYW1scD0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOnByb3RvY29sIj48c2FtbDpJc3N1ZXIgU1BQcm92aWRlZElEPSIyLjE2LjEwMC4xLjEuMS4xLjE4Ij5odHRwczovL3Jpci5lZ292LmJnPC9zYW1sOklzc3Vlcj48U2lnbmF0dXJlIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjIj48U2lnbmVkSW5mbz48Q2Fub25pY2FsaXphdGlvbk1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnL1RSLzIwMDEvUkVDLXhtbC1jMTRuLTIwMDEwMzE1IiAvPjxTaWduYXR1cmVNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjcnNhLXNoYTEiIC8+PFJlZmVyZW5jZSBVUkk9IiI+PFRyYW5zZm9ybXM+PFRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNlbnZlbG9wZWQtc2lnbmF0dXJlIiAvPjxUcmFuc2Zvcm0gQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy14bWwtYzE0bi0yMDAxMDMxNSIgLz48L1RyYW5zZm9ybXM+PERpZ2VzdE1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNzaGExIiAvPjxEaWdlc3RWYWx1ZT5yQUowdmZrTFJqbm1BeE5FV0pWdk9zVkxDTlk9PC9EaWdlc3RWYWx1ZT48L1JlZmVyZW5jZT48L1NpZ25lZEluZm8+PFNpZ25hdHVyZVZhbHVlPmkzaVZBVGdJMzdqcDJmcW9IWWlVY24xckZmSG9pc3hsdlpSOVJGQ2xFdm9UeTNqQ2ZETFFUcTRRV0QrZy9mV1hVbnhzRnhSUVI0YXZIUkRVU3pZelFQUFZNemVCa2tXbWE1ZUdQSVhhSVp5TVdua2g3UmdQMWVEK2VoQUJ1Vy9NTkJrQnl1YmRoRm9JTk9uMmxGRXhJRkFhQStWV2NHeFdLT0tXRHdMWkZuL1gyMGhHL3NCaURFVHNIRkpWc1QzVERreWVBbHY2aGJnR0xWOHNsZXg0SU9hNVR0QXUzSm1ZdEQwc3pKcnFRN0pPeXg1WHBNUlNhRWYyY1pHL3FPaEdHVmlKcXJoanRhYVp5aVdnYkRHOUlQQWdyUlloK0NOSlFJRnRLMVYwTU5Wam1oa0xBbldSM2IvL2NISWd2dG1TdTVjdmFXOGdVWTVsN0dMOXMrRDNYdz09PC9TaWduYXR1cmVWYWx1ZT48S2V5SW5mbz48WDUwOURhdGE+PFg1MDlJc3N1ZXJTZXJpYWw+PFg1MDlJc3N1ZXJOYW1lPkM9VVMsIE89TGV0J3MgRW5jcnlwdCwgQ049TGV0J3MgRW5jcnlwdCBBdXRob3JpdHkgWDM8L1g1MDlJc3N1ZXJOYW1lPjxYNTA5U2VyaWFsTnVtYmVyPjI4MTQ3NDk3NjQ0ODUxMTwvWDUwOVNlcmlhbE51bWJlcj48L1g1MDlJc3N1ZXJTZXJpYWw+PFg1MDlTdWJqZWN0TmFtZT5DTj1zdGFnZXJpci5lZ292LmJnPC9YNTA5U3ViamVjdE5hbWU+PFg1MDlDZXJ0aWZpY2F0ZT5NSUlHQ2pDQ0JQS2dBd0lCQWdJU0EvZ0xhTWJINFovVzAwQnNLdFdsUzhjVk1BMEdDU3FHU0liM0RRRUJDd1VBTUVveEN6QUpCZ05WQkFZVEFsVlRNUll3RkFZRFZRUUtFdzFNWlhRbmN5QkZibU55ZVhCME1TTXdJUVlEVlFRREV4cE1aWFFuY3lCRmJtTnllWEIwSUVGMWRHaHZjbWwwZVNCWU16QWVGdzB4T0RBNE1qTXdOelEyTkRGYUZ3MHhPREV4TWpFd056UTJOREZhTUJzeEdUQVhCZ05WQkFNVEVITjBZV2RsY21seUxtVm5iM1l1WW1jd2dnRWlNQTBHQ1NxR1NJYjNEUUVCQVFVQUE0SUJEd0F3Z2dFS0FvSUJBUURTVUVRYWRWVStaTkV3VmZvZUdDTDQ5NXhqaXltRXJWeWdqR2RDUTY1dXlEdEtJVjRFRlpuS29IWlJueG1GZVhqL0hCekoxak13QkE0V0l1b3Nud0l1ZXF5RWdKVmkyZHcxc3owRmE5RUVTS3RKM2s2SkJkVjNTOTNkU0RicktRWHVnQTZVeTQ4N0ppdU5HL0dnS2ZoNnhYZStuMHhXOXpBeXVzSkM1UWw0d0xwb2h5akl0UVhiWGJZL01meml4MjNVNktMMTJHdkx1ck4vTHlQc2UrOGowOUtBUi9DRWgxYzJCVzNjYW9XREF6Ym9NUnE0RUxlOEMwck1xS1VFTE9nY2JXR2hKWm1RY3BQNUM1YXRkeStGRmdpYVhoc2JXZ3FPbGJCWjIwbUhlbm9saDc3clEwKytid21LUkR4d2t5UDFYdlRDbnY1NkZXeDlnSGZSd0VtSEFnTUJBQUdqZ2dNWE1JSURFekFPQmdOVkhROEJBZjhFQkFNQ0JhQXdIUVlEVlIwbEJCWXdGQVlJS3dZQkJRVUhBd0VHQ0NzR0FRVUZCd01DTUF3R0ExVWRFd0VCL3dRQ01BQXdIUVlEVlIwT0JCWUVGSGdieTNYOWozMWxwdjNFeC83NUprK2ErYUVaTUI4R0ExVWRJd1FZTUJhQUZLaEthbU1FZmQyNjV0RTV0NlpGWmUvenFPeWhNRzhHQ0NzR0FRVUZCd0VCQkdNd1lUQXVCZ2dyQmdFRkJRY3dBWVlpYUhSMGNEb3ZMMjlqYzNBdWFXNTBMWGd6TG14bGRITmxibU55ZVhCMExtOXlaekF2QmdnckJnRUZCUWN3QW9ZamFIUjBjRG92TDJObGNuUXVhVzUwTFhnekxteGxkSE5sYm1OeWVYQjBMbTl5Wnk4d0d3WURWUjBSQkJRd0VvSVFjM1JoWjJWeWFYSXVaV2R2ZGk1aVp6Q0IvZ1lEVlIwZ0JJSDJNSUh6TUFnR0JtZUJEQUVDQVRDQjVnWUxLd1lCQkFHQzN4TUJBUUV3Z2RZd0pnWUlLd1lCQlFVSEFnRVdHbWgwZEhBNkx5OWpjSE11YkdWMGMyVnVZM0o1Y0hRdWIzSm5NSUdyQmdnckJnRUZCUWNDQWpDQm5neUJtMVJvYVhNZ1EyVnlkR2xtYVdOaGRHVWdiV0Y1SUc5dWJIa2dZbVVnY21Wc2FXVmtJSFZ3YjI0Z1lua2dVbVZzZVdsdVp5QlFZWEowYVdWeklHRnVaQ0J2Ym14NUlHbHVJR0ZqWTI5eVpHRnVZMlVnZDJsMGFDQjBhR1VnUTJWeWRHbG1hV05oZEdVZ1VHOXNhV041SUdadmRXNWtJR0YwSUdoMGRIQnpPaTh2YkdWMGMyVnVZM0o1Y0hRdWIzSm5MM0psY0c5emFYUnZjbmt2TUlJQkF3WUtLd1lCQkFIV2VRSUVBZ1NCOUFTQjhRRHZBSFVBVllIVXdoYVFOZ0ZLNmd1YlZ6eFQ4TURrT0hod0pRZ1hMNk9xSFFjVDB3d0FBQUZsWmZiWitRQUFCQU1BUmpCRUFpQmxhalhxRmhhVmhVbDFXUzZrM244QmxlKytYTHdXbXgrd0pYTkJGNDFkYUFJZ0VXVHh3Nmw2VFljRHYvR1lLS0Q4a3F2OTRsOHNNZFJmYURLZ1dJZ2ZjWmdBZGdBcFBGR1dWTWc1WmJxcVVQeFlCOVMzYjc5WWVpbHkzS1RERFBUbFJVZjBlQUFBQVdWbDl0bmxBQUFFQXdCSE1FVUNJQ3d6ZnBBQVU1WWZ2R1lsYTdCQnpLbnN0RDFENTd6S2VXaWpoRmZoR0oxc0FpRUFzbjdoRjhBVWRwWnpCMmFmNXNXeGhreWt6OFZxWkszcThBdGNJeWlwS2pzd0RRWUpLb1pJaHZjTkFRRUxCUUFEZ2dFQkFDdnBvQk1aOUtxbzVGcXJFRW5uT2Uxa2ZIWmZmQUlobTRqRHR1bFNQdGt6MlhRNW90TnNMM2FQM1M1ZTh6YVIwc2ovUnlmSjlBUStLNFFBZWdPV0JJMnp6Zm05bUdUY1pmQVVyRVQzRndBSjRYNEo1SkJhcVd6ZXdsNldrMHlOUnBSTHdQZlNuN0U4cFAyRFdvQ3BqaTFVbkVlMUR3UGZaN2JmbDBHSXUrckhNV09hdjdJdjlVVXFvZ3MwU280TUNHUlgzSmdGazNDUVdSdzBWc3BDSEdCcVNxRS9HQUlnb1BEQmQ5MzRnaEhSVWcxeTh5VS9xTzRsK2d0SUZYOHFqZDJVeVRzRXJaVVdGZnVSaTVVd3RIK1U5bGM1Z0k5eVltV0dLdXY0d1lLV3I3Zm43RUlOYVY1bnFZZk8wNGgxbDQxenN1a05WcjMrOE14OG54K2c3RGc9PC9YNTA5Q2VydGlmaWNhdGU+PC9YNTA5RGF0YT48L0tleUluZm8+PC9TaWduYXR1cmU+PHNhbWxwOkV4dGVuc2lvbnM+PGVnb3ZiZ2E6UmVxdWVzdGVkU2VydmljZT48ZWdvdmJnYTpTZXJ2aWNlPjIuMTYuMTAwLjEuMS4xLjEuMTguMS4xLjE8L2Vnb3ZiZ2E6U2VydmljZT48ZWdvdmJnYTpQcm92aWRlcj4yLjE2LjEwMC4xLjEuMS4xLjE4PC9lZ292YmdhOlByb3ZpZGVyPjwvZWdvdmJnYTpSZXF1ZXN0ZWRTZXJ2aWNlPjwvc2FtbHA6RXh0ZW5zaW9ucz48L3NhbWxwOkF1dGhuUmVxdWVzdD4=\" />\n");
			$this->add("</form>\n");
			$this->add(Dict::S('UI:Login:About'));
			$this->add("</div>\n");
			break;
		}
	}

	/**
	 * Return '' to disable this feature	
	 */	
	public function ForgotPwdLink()
	{
		$sUrl = utils::GetAbsoluteUrlAppRoot() . 'pages/UI.php?loginop=forgot_pwd';
		$sHtml = "<a href=\"$sUrl\" target=\"_blank\">".Dict::S('UI:Login:ForgotPwd')."</a>";
		return $sHtml;
	}

	public function DisplayForgotPwdForm($bFailedToReset = false, $sFailureReason = null)
	{
		$this->DisplayLoginHeader();
		$this->add("<div id=\"login\">\n");
		$this->add("<h1>".Dict::S('UI:Login:ForgotPwdForm')."</h1>\n");
		$this->add("<p>".Dict::S('UI:Login:ForgotPwdForm+')."</p>\n");
		if ($bFailedToReset)
		{
			$this->add("<p class=\"hilite\">".Dict::Format('UI:Login:ResetPwdFailed', htmlentities($sFailureReason, ENT_QUOTES, 'UTF-8'))."</p>\n");
		}
		$sAuthUser = utils::ReadParam('auth_user', '', true, 'raw_data');
		$this->add("<form method=\"post\">\n");
		$this->add("<table>\n");
		$this->add("<tr><td colspan=\"2\" class=\"center\"><label for=\"user\">".Dict::S('UI:Login:UserNamePrompt').":</label><input id=\"user\" type=\"text\" name=\"auth_user\" value=\"".htmlentities($sAuthUser, ENT_QUOTES, 'UTF-8')."\" /></td></tr>\n");
		$this->add("<tr><td colspan=\"2\" class=\"center v-spacer\"><span class=\"btn_border\"><input type=\"button\" onClick=\"window.close();\" value=\"".Dict::S('UI:Button:Cancel')."\" /></span>&nbsp;&nbsp;<span class=\"btn_border\"><input type=\"submit\" value=\"".Dict::S('UI:Login:ResetPassword')."\" /></span></td></tr>\n");
		$this->add("</table>\n");
		$this->add("<input type=\"hidden\" name=\"loginop\" value=\"forgot_pwd_go\" />\n");
		$this->add("</form>\n");
		$this->add("</div>\n");

		$this->add_ready_script('$("#user").focus();');
	}

	protected function ForgotPwdGo()
	{
		$sAuthUser = utils::ReadParam('auth_user', '', true, 'raw_data');

		try
		{
			UserRights::Login($sAuthUser); // Set the user's language (if possible!)
			$oUser = UserRights::GetUserObject();
			if ($oUser == null)
			{
				throw new Exception(Dict::Format('UI:ResetPwd-Error-WrongLogin', $sAuthUser));
			}
			if (!MetaModel::IsValidAttCode(get_class($oUser), 'reset_pwd_token'))
			{
				throw new Exception(Dict::S('UI:ResetPwd-Error-NotPossible'));
			}
			if (!$oUser->CanChangePassword())
			{
				throw new Exception(Dict::S('UI:ResetPwd-Error-FixedPwd'));
			}
			
			$sTo = $oUser->GetResetPasswordEmail(); // throws Exceptions if not allowed
			if ($sTo == '')
			{
				throw new Exception(Dict::S('UI:ResetPwd-Error-NoEmail'));
			}

			// This token allows the user to change the password without knowing the previous one
			$sToken = substr(md5(APPROOT.uniqid()), 0, 16);
			$oUser->Set('reset_pwd_token', $sToken);
			CMDBObject::SetTrackInfo('Reset password');
			$oUser->DBUpdate();

			$oEmail = new Email();
			$oEmail->SetRecipientTO($sTo);
			$sFrom = MetaModel::GetConfig()->Get('forgot_password_from');
			$oEmail->SetRecipientFrom($sFrom);
			$oEmail->SetSubject(Dict::S('UI:ResetPwd-EmailSubject'));
			$sResetUrl = utils::GetAbsoluteUrlAppRoot().'pages/UI.php?loginop=reset_pwd&auth_user='.urlencode($oUser->Get('login')).'&token='.urlencode($sToken);
			$oEmail->SetBody(Dict::Format('UI:ResetPwd-EmailBody', $sResetUrl));
			$iRes = $oEmail->Send($aIssues, true /* force synchronous exec */);
			switch ($iRes)
			{
				//case EMAIL_SEND_PENDING:
				case EMAIL_SEND_OK:
					break;
		
				case EMAIL_SEND_ERROR:
				default:
					IssueLog::Error('Failed to send the email with the NEW password for '.$oUser->Get('friendlyname').': '.implode(', ', $aIssues));
					throw new Exception(Dict::S('UI:ResetPwd-Error-Send'));
			}

			$this->DisplayLoginHeader();
			$this->add("<div id=\"login\">\n");
			$this->add("<h1>".Dict::S('UI:Login:ForgotPwdForm')."</h1>\n");
			$this->add("<p>".Dict::S('UI:ResetPwd-EmailSent')."</p>");
			$this->add("<form method=\"post\">\n");
			$this->add("<table>\n");
			$this->add("<tr><td colspan=\"2\" class=\"center v-spacer\"><input type=\"button\" onClick=\"window.close();\" value=\"".Dict::S('UI:Button:Done')."\" /></td></tr>\n");
			$this->add("</table>\n");
			$this->add("</form>\n");
			$this->add("</div\n");
		}
		catch(Exception $e)
		{
			$this->DisplayForgotPwdForm(true, $e->getMessage());
		}
	}

	public function DisplayResetPwdForm()
	{
		$sAuthUser = utils::ReadParam('auth_user', '', false, 'raw_data');
		$sToken = utils::ReadParam('token', '', false, 'raw_data');

		UserRights::Login($sAuthUser); // Set the user's language
		$oUser = UserRights::GetUserObject();

		$this->DisplayLoginHeader();
		$this->add("<div id=\"login\">\n");
		$this->add("<h1>".Dict::S('UI:ResetPwd-Title')."</h1>\n");
		if ($oUser == null)
		{
			$this->add("<p>".Dict::Format('UI:ResetPwd-Error-WrongLogin', $sAuthUser)."</p>\n");
		}
		else
		{
			$oEncryptedToken = $oUser->Get('reset_pwd_token');
			
			if (!$oEncryptedToken->CheckPassword($sToken))
			{
				$this->add("<p>".Dict::S('UI:ResetPwd-Error-InvalidToken')."</p>\n");
			}
			else
			{
				$this->add("<p>".Dict::Format('UI:ResetPwd-Error-EnterPassword', $oUser->GetFriendlyName())."</p>\n");
	
				$sInconsistenPwdMsg = Dict::S('UI:Login:RetypePwdDoesNotMatch');
				$this->add_script(
<<<EOF
function DoCheckPwd()
{
	if ($('#new_pwd').val() != $('#retype_new_pwd').val())
	{
		alert('$sInconsistenPwdMsg');
		return false;
	}
	return true;
}
EOF
				);
				$this->add("<form method=\"post\">\n");
				$this->add("<table>\n");
				$this->add("<tr><td style=\"text-align:right\"><label for=\"new_pwd\">".Dict::S('UI:Login:NewPasswordPrompt').":</label></td><td style=\"text-align:left\"><input type=\"password\" id=\"new_pwd\" name=\"new_pwd\" value=\"\" /></td></tr>\n");
				$this->add("<tr><td style=\"text-align:right\"><label for=\"retype_new_pwd\">".Dict::S('UI:Login:RetypeNewPasswordPrompt').":</label></td><td style=\"text-align:left\"><input type=\"password\" id=\"retype_new_pwd\" name=\"retype_new_pwd\" value=\"\" /></td></tr>\n");
				$this->add("<tr><td colspan=\"2\" class=\"center v-spacer\"><span class=\"btn_border\"><input type=\"submit\" onClick=\"return DoCheckPwd();\" value=\"".Dict::S('UI:Button:ChangePassword')."\" /></span></td></tr>\n");
				$this->add("</table>\n");
				$this->add("<input type=\"hidden\" name=\"loginop\" value=\"do_reset_pwd\" />\n");
				$this->add("<input type=\"hidden\" name=\"auth_user\" value=\"".htmlentities($sAuthUser, ENT_QUOTES, 'UTF-8')."\" />\n");
				$this->add("<input type=\"hidden\" name=\"token\" value=\"".htmlentities($sToken, ENT_QUOTES, 'UTF-8')."\" />\n");
				$this->add("</form>\n");
				$this->add("</div\n");
			}
		}
	}

	public function DoResetPassword()
	{
		$sAuthUser = utils::ReadParam('auth_user', '', false, 'raw_data');
		$sToken = utils::ReadParam('token', '', false, 'raw_data');
		$sNewPwd = utils::ReadPostedParam('new_pwd', '', false, 'raw_data');

		UserRights::Login($sAuthUser); // Set the user's language
		$oUser = UserRights::GetUserObject();

		$this->DisplayLoginHeader();
		$this->add("<div id=\"login\">\n");
		$this->add("<h1>".Dict::S('UI:ResetPwd-Title')."</h1>\n");
		if ($oUser == null)
		{
			$this->add("<p>".Dict::Format('UI:ResetPwd-Error-WrongLogin', $sAuthUser)."</p>\n");
		}
		else
		{
			$oEncryptedPassword = $oUser->Get('reset_pwd_token');
			if (!$oEncryptedPassword->CheckPassword($sToken))
			{
				$this->add("<p>".Dict::S('UI:ResetPwd-Error-InvalidToken')."</p>\n");
			}
			else
			{
				// Trash the token and change the password
				$oUser->Set('reset_pwd_token', '');
				$oUser->SetPassword($sNewPwd); // Does record the change into the DB
	
				$this->add("<p>".Dict::S('UI:ResetPwd-Ready')."</p>");
				$sUrl = utils::GetAbsoluteUrlAppRoot();
				$this->add("<p><a href=\"$sUrl\">".Dict::S('UI:ResetPwd-Login')."</a></p>");
			}
			$this->add("</div\n");
		}
	}

	public function DisplayChangePwdForm($bFailedLogin = false)
	{
		$sAuthUser = utils::ReadParam('auth_user', '', false, 'raw_data');

		$sInconsistenPwdMsg = Dict::S('UI:Login:RetypePwdDoesNotMatch');
		$this->add_script(<<<EOF
function GoBack()
{
	window.history.back();
}

function DoCheckPwd()
{
	if (!$('#new_pwd').val().match(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$&()\\-`.+]{8,}$/))
	{
		alert('Паролата трябва да съдържа минимум 8 символа, главна, малка буква и число');
		return false;
	}
	else if ($('#new_pwd').val() != $('#retype_new_pwd').val())
	{
		alert('$sInconsistenPwdMsg');
		return false;
	}
	return true;
}
EOF
);
		$this->DisplayLoginHeader();
		$this->add("<div id=\"login\">\n");
		$this->add("<h1>".Dict::S('UI:Login:ChangeYourPassword')."</h1>\n");
		if ($bFailedLogin)
		{
			$this->add("<p class=\"hilite\">".Dict::S('UI:Login:IncorrectOldPassword')."</p>\n");
		}
		$this->add("<form method=\"post\">\n");
		$this->add("<table>\n");
		$this->add("<tr><td style=\"text-align:right\"><label for=\"old_pwd\">".Dict::S('UI:Login:OldPasswordPrompt').":</label></td><td style=\"text-align:left\"><input type=\"password\" id=\"old_pwd\" name=\"old_pwd\" value=\"\" /></td></tr>\n");
		$this->add("<tr><td style=\"text-align:right\"><label for=\"new_pwd\">".Dict::S('UI:Login:NewPasswordPrompt').":</label></td><td style=\"text-align:left\"><input type=\"password\" id=\"new_pwd\" name=\"new_pwd\" value=\"\" /></td></tr>\n");
		$this->add("<tr><td style=\"text-align:right\"><label for=\"retype_new_pwd\">".Dict::S('UI:Login:RetypeNewPasswordPrompt').":</label></td><td style=\"text-align:left\"><input type=\"password\" id=\"retype_new_pwd\" name=\"retype_new_pwd\" value=\"\" /></td></tr>\n");
		$this->add("<tr><td colspan=\"2\" class=\"center v-spacer\"><span class=\"btn_border\"><input type=\"button\" onClick=\"GoBack();\" value=\"".Dict::S('UI:Button:Cancel')."\" /></span>&nbsp;&nbsp;<span class=\"btn_border\"><input type=\"submit\" onClick=\"return DoCheckPwd();\" value=\"".Dict::S('UI:Button:ChangePassword')."\" /></span></td></tr>\n");
		$this->add("</table>\n");
		$this->add("<input type=\"hidden\" name=\"loginop\" value=\"do_change_pwd\" />\n");
		$this->add("</form>\n");
		$this->add("</div>\n");
	}

	static function ResetSession()
	{
		// Unset all of the session variables.
		unset($_SESSION['auth_user']);
		unset($_SESSION['login_mode']);
		unset($_SESSION['archive_mode']);
		unset($_SESSION['impersonate_user']);
		UserRights::_ResetSessionCache();
		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
	}

	static function SecureConnectionRequired()
	{
		return MetaModel::GetConfig()->GetSecureConnectionRequired();
	}

	/**
	 * Guess if a string looks like an UTF-8 string based on some ranges of multi-bytes encoding
	 * @param string $sString
	 * @return bool True if the string contains some typical UTF-8 multi-byte sequences
	 */
	static function LooksLikeUTF8($sString)
	{
		return preg_match('%(?:
        			[\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        			|\xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        			|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
        			|\xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        			|\xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        			|[\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
        			|\xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        	)+%xs', $sString);
	}

	/**
	 * Attempt a login
	 * 	 	
	 * @param int iOnExit What action to take if the user is not logged on (one of the class constants EXIT_...)
	 * @return int One of the class constants EXIT_CODE_...
	 */	
	protected static function Login($iOnExit)
	{
		if (self::SecureConnectionRequired() && !utils::IsConnectionSecure())
		{
			// Non secured URL... request for a secure connection
			throw new Exception('Secure connection required!');			
		}

		$aAllowedLoginTypes = MetaModel::GetConfig()->GetAllowedLoginTypes();

		if (isset($_SESSION['auth_user']))
		{
			//echo "User: ".$_SESSION['auth_user']."\n";
			// Already authentified
			$bRet = UserRights::Login($_SESSION['auth_user']); // Login & set the user's language
			if ($bRet)
			{
				return self::EXIT_CODE_OK;
			}
			// The user account is no longer valid/enabled
			 static::ResetSession();
		}
		
		$index = 0;
		$sLoginMode = '';
		$sAuthentication = 'internal';
		while(($sLoginMode == '') && ($index < count($aAllowedLoginTypes)))
		{
			$sLoginType = $aAllowedLoginTypes[$index];
			switch($sLoginType)
			{
				case 'cas':
				utils::InitCASClient();					
				// check CAS authentication
				if (phpCAS::isAuthenticated())
				{
					$sAuthUser = phpCAS::getUser();
					$sAuthPwd = '';
					$sLoginMode = 'cas';
					$sAuthentication = 'external';
				}
				break;
				
				case 'form':
				// iTop standard mode: form based authentication
				$sAuthUser = utils::ReadPostedParam('auth_user', '', false, 'raw_data');
				$sAuthPwd = utils::ReadPostedParam('auth_pwd', null, false, 'raw_data');
				if (($sAuthUser != '') && ($sAuthPwd !== null))
				{
					$sLoginMode = 'form';
				}
				break;
				
				case 'basic':
				// Standard PHP authentication method, works with Apache...
				// Case 1) Apache running in CGI mode + rewrite rules in .htaccess
				if (isset($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['HTTP_AUTHORIZATION']))
				{
					list($sAuthUser, $sAuthPwd) = explode(':' , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
					$sLoginMode = 'basic';
				}
				else if (isset($_SERVER['PHP_AUTH_USER']))
				{
					$sAuthUser = $_SERVER['PHP_AUTH_USER'];
					// Unfortunately, the RFC is not clear about the encoding...
					// IE and FF supply the user and password encoded in ISO-8859-1 whereas Chrome provides them encoded in UTF-8
					// So let's try to guess if it's an UTF-8 string or not... fortunately all encodings share the same ASCII base
					if (!self::LooksLikeUTF8($sAuthUser))
					{
						// Does not look like and UTF-8 string, try to convert it from iso-8859-1 to UTF-8
						// Supposed to be harmless in case of a plain ASCII string...
						$sAuthUser = iconv('iso-8859-1', 'utf-8', $sAuthUser);
					}
					$sAuthPwd = $_SERVER['PHP_AUTH_PW'];
					if (!self::LooksLikeUTF8($sAuthPwd))
					{
						// Does not look like and UTF-8 string, try to convert it from iso-8859-1 to UTF-8
						// Supposed to be harmless in case of a plain ASCII string...
						$sAuthPwd = iconv('iso-8859-1', 'utf-8', $sAuthPwd);
					}
					$sLoginMode = 'basic';
				}
				break;

				case 'external':
				// Web server supplied authentication
				$bExternalAuth = false;
				$sExtAuthVar = MetaModel::GetConfig()->GetExternalAuthenticationVariable(); // In which variable is the info passed ?
				eval('$sAuthUser = isset('.$sExtAuthVar.') ? '.$sExtAuthVar.' : false;'); // Retrieve the value
				if ($sAuthUser && (strlen($sAuthUser) > 0))
				{
					$sAuthPwd = ''; // No password in this case the web server already authentified the user...
					$sLoginMode = 'external';
					$sAuthentication = 'external';
				}
				break;

				case 'url':
				// Credentials passed directly in the url
				$sAuthUser = utils::ReadParam('auth_user', '', false, 'raw_data');
				$sAuthPwd = utils::ReadParam('auth_pwd', null, false, 'raw_data');
				if (($sAuthUser != '') && ($sAuthPwd !== null))
				{
					$sLoginMode = 'url';
				}		
				break;	
			}
			$index++;
		}
		//echo "\nLoginMode: $sLoginMode (user: $sAuthUser / pwd: $sAuthPwd\n)";
		if ($sLoginMode == '')
		{
			// First connection
			$sDesiredLoginMode = utils::ReadParam('login_mode');
			if (in_array($sDesiredLoginMode, $aAllowedLoginTypes))
			{
				$sLoginMode = $sDesiredLoginMode;
			}
			else
			{
				$sLoginMode = $aAllowedLoginTypes[0]; // First in the list...
			}
			if (array_key_exists('HTTP_X_COMBODO_AJAX', $_SERVER))
			{
				// X-Combodo-Ajax is a special header automatically added to all ajax requests
				// Let's reply that we're currently logged-out
				header('HTTP/1.0 401 Unauthorized');
				exit;
			}
			if (($iOnExit == self::EXIT_HTTP_401) || ($sLoginMode == 'basic'))
			{
				header('WWW-Authenticate: Basic realm="'.Dict::Format('UI:iTopVersion:Short', ITOP_APPLICATION, ITOP_VERSION));
				header('HTTP/1.0 401 Unauthorized');
				header('Content-type: text/html; charset=iso-8859-1');
				exit;
			}
			else if($iOnExit == self::EXIT_RETURN)
			{
				if (($sAuthUser !== '') && ($sAuthPwd === null))
				{
					return self::EXIT_CODE_MISSINGPASSWORD;
				}
				/////// NEW PASSWORD Streinght 
				else if (($sAuthUser !== '') && (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$&()\\-`.+]{8,}$/",$sAuthPwd)))
				{
					return self::EXIT_CODE_MISSINGPASSWORD;
				}
				////////
				else
				{
					return self::EXIT_CODE_MISSINGLOGIN;
				}
			}
			else
			{
				$oPage = self::NewLoginWebPage();
				$oPage->DisplayLoginForm( $sLoginMode, false /* no previous failed attempt */);
				$oPage->output();
				exit;
			}
		}
		else
		{
			if (!UserRights::CheckCredentials($sAuthUser, $sAuthPwd, $sLoginMode, $sAuthentication))
			{
				//echo "Check Credentials returned false for user " . $sAuthUser;
				self::ResetSession();
				if (($iOnExit == self::EXIT_HTTP_401) || ($sLoginMode == 'basic'))
				{
					header('WWW-Authenticate: Basic realm="'.Dict::Format('UI:iTopVersion:Short', ITOP_APPLICATION, ITOP_VERSION));
					header('HTTP/1.0 401 Unauthorized');
					header('Content-type: text/html; charset=iso-8859-1');
					exit;
				}
				else if($iOnExit == self::EXIT_RETURN)
				{
					return self::EXIT_CODE_WRONGCREDENTIALS;
				}
				else
				{
				    //////// NEW REDIRECT TO Registration DAEU
				    if($sLoginMode == 'external') header("Location: https://rir.egov.bg/error.html");
				    ////////
				    
					$oPage = self::NewLoginWebPage();
					$oPage->DisplayLoginForm( $sLoginMode, true /* failed attempt */);
					$oPage->output();
					exit;
				}
			}
			else
			{
				// User is Ok, let's save it in the session and proceed with normal login
				UserRights::Login($sAuthUser, $sAuthentication); // Login & set the user's language
				
				$_SESSION['auth_user'] = $sAuthUser;
				$_SESSION['login_mode'] = $sLoginMode;
				UserRights::_InitSessionCache();
				
				if (MetaModel::GetConfig()->Get('log_usage'))
				{
					$oLog = new EventLoginUsage();
					$oLog->Set('userinfo', UserRights::GetUser());
					$oLog->Set('user_id', UserRights::GetUserObject()->GetKey());
					$oLog->Set('message', 'Successful login');
					//$oLog->DBInsertNoReload();
					
					$oSearch = DBObjectSearch::FromOQL('SELECT EventLoginUsage WHERE user_id =:user_id');
					$oSet = new DBObjectSet($oSearch, array(), array('user_id' => UserRights::GetUserObject()->GetKey()));
					
					if($oSet->Count() == 0 && $sLoginMode == 'form') {
					// NEW User, reset password
					// Log it
					  $oLog->DBInsertNoReload();
					// Redirect to passchange
					 header("Location: ".utils::GetAbsoluteUrlAppRoot()."/pages/UI.php?loginop=change_pwd"); 
					 exit;
					} else {
					// Only Log
					  $oLog->DBInsertNoReload();
					}
				}
				
				//$_SESSION['auth_user'] = $sAuthUser;
				//$_SESSION['login_mode'] = $sLoginMode;
				//UserRights::_InitSessionCache();
			}
		}
		return self::EXIT_CODE_OK;
	}
	
	/**
	 * Overridable: depending on the user, head toward a dedicated portal
	 * @param string|null $sRequestedPortalId
	 * @param int $iOnExit How to complete the call: redirect or return a code
	 */	 
	protected static function ChangeLocation($sRequestedPortalId = null, $iOnExit = self::EXIT_PROMPT)
	{
		$fStart = microtime(true);
		$ret = call_user_func(array(self::$sHandlerClass, 'Dispatch'), $sRequestedPortalId);
		if ($ret === true)
		{
			return self::EXIT_CODE_OK;
		}
		else if($ret === false)
		{
			throw new Exception('Nowhere to go??');
		}
		else
		{
			if ($iOnExit == self::EXIT_RETURN)
			{
				return self::EXIT_CODE_PORTALUSERNOTAUTHORIZED;
			}
			else
			{
				// No rights to be here, redirect to the portal
				header('Location: '.$ret);
			}
		}
	}

	/**
	 * Check if the user is already authentified, if yes, then performs some additional validations:
	 * - if $bMustBeAdmin is true, then the user must be an administrator, otherwise an error is displayed
	 * - if $bIsAllowedToPortalUsers is false and the user has only access to the portal, then the user is redirected
	 * to the portal
	 *
	 * @param bool $bMustBeAdmin Whether or not the user must be an admin to access the current page
	 * @param bool $bIsAllowedToPortalUsers Whether or not the current page is considered as part of the portal
	 * @param int iOnExit What action to take if the user is not logged on (one of the class constants EXIT_...)
	 *
	 * @return int|mixed|string
	 * @throws \Exception
	 */
	static function DoLogin($bMustBeAdmin = false, $bIsAllowedToPortalUsers = false, $iOnExit = self::EXIT_PROMPT)
	{
		$sRequestedPortalId = $bIsAllowedToPortalUsers ? 'legacy_portal' : 'backoffice';
		return self::DoLoginEx($sRequestedPortalId, $bMustBeAdmin, $iOnExit);
	}

	/**
	 * Check if the user is already authentified, if yes, then performs some additional validations to redirect towards
	 * the desired "portal"
	 *
	 * @param string|null $sRequestedPortalId The requested "portal" interface, null for any
	 * @param bool $bMustBeAdmin Whether or not the user must be an admin to access the current page
	 * @param int iOnExit What action to take if the user is not logged on (one of the class constants EXIT_...)
	 *
	 * @return int|mixed|string
	 * @throws \Exception
	 */
	static function DoLoginEx($sRequestedPortalId = null, $bMustBeAdmin = false, $iOnExit = self::EXIT_PROMPT)
	{
		$operation = utils::ReadParam('loginop', '');
	
		$sMessage = self::HandleOperations($operation); // May exit directly
	
		$iRet = self::Login($iOnExit);
	
		if ($iRet == self::EXIT_CODE_OK)
		{
			if ($bMustBeAdmin && !UserRights::IsAdministrator())
			{
				if ($iOnExit == self::EXIT_RETURN)
				{
					return self::EXIT_CODE_MUSTBEADMIN;
				}
				else
				{
					require_once(APPROOT.'/setup/setuppage.class.inc.php');
					$oP = new SetupPage(Dict::S('UI:PageTitle:FatalError'));
					$oP->add("<h1>".Dict::S('UI:Login:Error:AccessAdmin')."</h1>\n");
					$oP->p("<a href=\"".utils::GetAbsoluteUrlAppRoot()."pages/logoff.php\">".Dict::S('UI:LogOffMenu')."</a>");
					$oP->output();
					exit;
				}
			}
			$iRet = call_user_func(array(self::$sHandlerClass, 'ChangeLocation'), $sRequestedPortalId, $iOnExit);
		}
		if ($iOnExit == self::EXIT_RETURN)
		{
			return $iRet;
		}
		else
		{
			return $sMessage;
		}
	}	
	protected static function HandleOperations($operation)
	{
		$sMessage = ''; // most of the operations never return, but some can return a message to be displayed
		if ($operation == 'logoff')
		{
			if (isset($_SESSION['login_mode']))
			{
				$sLoginMode = $_SESSION['login_mode'];
			}
			else
			{
				$aAllowedLoginTypes = MetaModel::GetConfig()->GetAllowedLoginTypes();
				if (count($aAllowedLoginTypes) > 0)
				{
					$sLoginMode = $aAllowedLoginTypes[0];
				}
				else
				{
					$sLoginMode = 'form';
				}
			}
			self::ResetSession();
			$oPage = self::NewLoginWebPage();
			$oPage->DisplayLoginForm( $sLoginMode, false /* not a failed attempt */);
			$oPage->output();
			exit;
		}
		else if ($operation == 'forgot_pwd')
		{
			$oPage = self::NewLoginWebPage();
			$oPage->DisplayForgotPwdForm();
			$oPage->output();
			exit;
		}
		else if ($operation == 'forgot_pwd_go')
		{
			$oPage = self::NewLoginWebPage();
			$oPage->ForgotPwdGo();
			$oPage->output();
			exit;
		}
		else if ($operation == 'reset_pwd')
		{
			$oPage = self::NewLoginWebPage();
			$oPage->DisplayResetPwdForm();
			$oPage->output();
			exit;
		}
		else if ($operation == 'do_reset_pwd')
		{
			$oPage = self::NewLoginWebPage();
			$oPage->DoResetPassword();
			$oPage->output();
			exit;
		}
		else if ($operation == 'change_pwd')
		{
			$sAuthUser = $_SESSION['auth_user'];
			UserRights::Login($sAuthUser); // Set the user's language
			$oPage = self::NewLoginWebPage();
			$oPage->DisplayChangePwdForm();
			$oPage->output();
			exit;
		}
		if ($operation == 'do_change_pwd')
		{
			$sAuthUser = $_SESSION['auth_user'];
			UserRights::Login($sAuthUser); // Set the user's language
			$sOldPwd = utils::ReadPostedParam('old_pwd', '', false, 'raw_data');
			$sNewPwd = utils::ReadPostedParam('new_pwd', '', false, 'raw_data');
			if (UserRights::CanChangePassword() && ((!UserRights::CheckCredentials($sAuthUser, $sOldPwd)) || (!UserRights::ChangePassword($sOldPwd, $sNewPwd))))
			{
				$oPage = self::NewLoginWebPage();
				$oPage->DisplayChangePwdForm(true); // old pwd was wrong
				$oPage->output();
				exit;
			}
			$sMessage = Dict::S('UI:Login:PasswordChanged');
		}
		return $sMessage;
	}
	
	protected static function Dispatch($sRequestedPortalId)
	{
		if ($sRequestedPortalId === null) return true; // allowed to any portal => return true
		
		$aPortalsConf = PortalDispatcherData::GetData();
		$aDispatchers = array();
		foreach($aPortalsConf as $sPortalId => $aConf)
		{
			$sHandlerClass = $aConf['handler'];
			$aDispatchers[$sPortalId] = new $sHandlerClass($sPortalId);
		}
		
		if (array_key_exists($sRequestedPortalId, $aDispatchers) && $aDispatchers[$sRequestedPortalId]->IsUserAllowed())
		{
			return true;
		}
		foreach($aDispatchers as $sPortalId => $oDispatcher)
		{
			if ($oDispatcher->IsUserAllowed()) return $oDispatcher->GetUrl();
		}
		return false; // nothing matched !!
	}
} // End of class
