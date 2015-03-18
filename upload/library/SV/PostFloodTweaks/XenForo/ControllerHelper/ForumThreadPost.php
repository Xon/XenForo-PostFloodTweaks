<?php

class SV_PostFloodTweaks_XenForo_ControllerHelper_ForumThreadPost extends XFCP_SV_PostFloodTweaks_XenForo_ControllerHelper_ForumThreadPost
{

    public function assertThreadValidAndViewable($threadId,
        array $threadFetchOptions = array(), array $forumFetchOptions = array()
    )
    {
        $readUserId = isset($threadFetchOptions['readUserId']) ? $threadFetchOptions['readUserId'] : 0;

        if($readUserId)
        {
            $key = $threadId.'_'.$readUserId;
            if (!isset($this->_controller->ftp_thread_cache))
            {
                $this->_controller->ftp_thread_cache = array();
            }

            if (isset($this->_controller->ftp_thread_cache[$key]))
            {
                return $this->_controller->ftp_thread_cache[$key];
            }
        }

        $response = parent::assertThreadValidAndViewable($threadId, $threadFetchOptions, $forumFetchOptions);

        if ($readUserId)
        {
            $this->_controller->ftp_thread_cache[$key] = $response;
        }

        return $response;
    }
}