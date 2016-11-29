<?php
namespace Nitsan\NsSocialStream\Controller;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility as Debug;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * SocialTimelineController
 */
class SocialTimelineController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{


    /**
     * title
     *
     * @var string
     */
    protected $errorMessage = '';

    public function initializeAction() {
        session_start();
        if(TYPO3_MODE == 'FE') {
            $extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ns_social_stream']);
            $script = "
            <script type='text/javascript'>
                $('#socialTimeline').dpSocialTimeline({
                    feeds: 
                        { ";

                       /*twitter Configuration*/
                        if($this->settings['plugin_type'] == 1){
                            $_SESSION['oauth_access_token'] = $extensionConfiguration['oauthAccessToken'];
                            $_SESSION['oauth_access_token_secret'] = $extensionConfiguration['oauthAccessTokenSecret'];
                            $_SESSION['consumer_key'] = $extensionConfiguration['consumerKey'];
                            $_SESSION['consumer_secret'] = $extensionConfiguration['consumerSecret'];

                            if($_SESSION['oauth_access_token'] == '' || $_SESSION['oauth_access_token_secret'] == '' || $_SESSION['consumer_key'] == '' || $_SESSION['consumer_secret'] == ''){

                            	$this->errorMessage = $GLOBALS['LANG']->sL('LLL:EXT:ns_social_stream/Resources/Private/Language/locallang_db.xlf:twitter.error');
                            }else{

                                if($this->settings['twitter_username']){
                                    $script .= "
                                    twitter: {data: 'typo3conf/ext/ns_social_stream/Resources/Private/PHP/twitter/user_timeline.php?screen_name=".$this->settings['twitter_username']."'},";
                                }
                                if($this->settings['twitter_hashtag']){
                                    $script .= "
                                    twitter_hash: {data: 'typo3conf/ext/ns_social_stream/Resources/Private/PHP/twitter/search.php?q=%23".$this->settings['twitter_hashtag']."'},";
                                }
                            }
                        }else{
                            unset ($_SESSION['oauth_access_token']);
                            unset ($_SESSION['oauth_access_token_secret']);
                            unset ($_SESSION['consumer_key']);
                            unset ($_SESSION['consumer_secret']);
                        }

                        /*twitter Configuration*/

                        /*facebook Configuration*/
                        if($this->settings['facebook_page_id'] && $this->settings['plugin_type'] == 2){
                            $pageId = $this->settings['facebook_page_id'];
                            $_SESSION['appId'] = $extensionConfiguration['facebookAppId'];
                            $_SESSION['secret'] = $extensionConfiguration['facebookSecretKey'];
                            if($_SESSION['appId'] == '' || $_SESSION['secret'] == ''){
                                $this->errorMessage = $GLOBALS['LANG']->sL('LLL:EXT:ns_social_stream/Resources/Private/Language/locallang_db.xlf:facebook.error');
                            }else{
                                $script .= "
                                facebook_page: {data: 'typo3conf/ext/ns_social_stream/Resources/Private/PHP/lib/facebook_page.php?page_id=".$pageId."'},";
                            }
                        }else{
                            unset ($_SESSION['appId']);
                            unset ($_SESSION['secret']);
                        }
                        /*facebook Configuration*/
                        
                        if($this->settings['plugin_type'] == 3){
                            if($this->settings['flickr_hashtag']){
                                $script .= "
                                flickr_hash: {data: '".$this->settings['flickr_hashtag']."'},";
                            }
                            if($this->settings['flickr_user_id']){
                                $script .= "
                                flickr: {data: '".$this->settings['flickr_user_id']."'},";
                            }
                        }
            $script .="
                    },
                    layoutMode: '".$this->settings['display_style']."',
                    skin: '".$this->settings['skin']."',
                    showSocialIcons: ".(($this->settings['show_icons'] == 1) ? 'true' : 'false').",
                    showFilter: ".(($this->settings['show_filter'] == 1) ? 'true' : 'false').",
                    showLayout: ".(($this->settings['show_layout'] == 1) ? 'true' : 'false').",
                    share: ".(($this->settings['show_share_buttons'] == 1) ? 'true' : 'false').",
                    addColorbox: ".(($this->settings['add_lightbox'] == 1) ? 'true' : 'false').",
                    total: '".(($this->settings['total'] != '') ? $this->settings['total'] : 20)."',
                    timelineItemWidth: '".(($this->settings['timelineItemWidth'] !='') ? $this->settings['timelineItemWidth'].'px' : $this->settings['timelineItemWidth_default'].'px')."',
                    columnsItemWidth: '".(($this->settings['columnsItemWidth'] !='') ? $this->settings['columnsItemWidth'].'px' : $this->settings['columnsItemWidth_default'].'px')."',
                    oneColumnItemWidth: '".(($this->settings['oneColumnItemWidth']!='') ? $this->settings['oneColumnItemWidth'].'%' : $this->settings['oneColumnItemWidth_default'].'%')."'
                    
                    });";
                    if($this->errorMessage){
		                $script ="<script type='text/javascript'></script>"; 
		            }else{
		            	$script .= "
		            </script>";
		            }
            $GLOBALS['TSFE']->additionalFooterData['ns_social_js'] = $script;
        
        }
    }
    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        if($this->errorMessage){
            $this->addFlashMessage($this->errorMessage, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        }
        $this->view->assign('socialTimelines', $socialTimelines);
    }
}