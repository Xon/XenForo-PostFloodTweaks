<?php

class SV_PostFloodTweaks_XenForo_ControllerPublic_Thread extends XFCP_SV_PostFloodTweaks_XenForo_ControllerPublic_Thread
{
    var $flood_enhance = false;
    var $flood_threadid = null;
    var $flood_nodeid = null;

    public function actionAddReply()
    {
        $this->_assertPostOnly();

        if ($this->_input->inRequest('more_options'))
        {
            return $this->responseReroute("XenForo_ControllerPublic_Thread", 'reply');
        }

        $threadId = $this->_input->filterSingle('thread_id', XenForo_Input::UINT);

        $visitor = XenForo_Visitor::getInstance();
        if (!$visitor->hasPermission('general', 'bypassFloodCheck'))
        {
            $ftpHelper = $this->getHelper('ForumThreadPost');
            $threadFetchOptions = array('readUserId' => $visitor['user_id']);
            $forumFetchOptions = array('readUserId' => $visitor['user_id']);
            list($thread, $forum) = $ftpHelper->assertThreadValidAndViewable($threadId, $threadFetchOptions, $forumFetchOptions);

            if (isset($thread['thread_id']))
            {
                $this->flood_threadid = $thread['thread_id'];
            }
            if (isset($forum['node_id']))
            {
                $this->flood_nodeid = $forum['node_id'];
            }
            $this->flood_enhance = true;
        }

        $response = parent::actionAddReply();

        $this->flood_threadid = null;
        $this->flood_nodeid = null;
        $this->flood_enhance = false;

        return $response;
    }


    public function assertNotFlooding($action, $floodingLimit = null)
    {
        $visitor = XenForo_Visitor::getInstance();
        if ($action == 'post' && $this->flood_enhance)
        {
            if (!empty($this->flood_threadid) && $visitor->hasPermission('forum', 'sv_postflood_thread_on'))
            {
                $rate_limit = $visitor->hasPermission('forum', 'sv_postflood_thread');
                if ($rate_limit < 0)
                {
                    return;
                }
                else if ($rate_limit > 0)
                {
                    parent::assertNotFlooding('t'.$this->flood_threadid, $rate_limit);
                    return;
                }
            }

            if (!empty($this->flood_nodeid) && $visitor->hasPermission('forum', 'sv_postflood_node_on'))
            {
                $rate_limit = $visitor->hasPermission('forum', 'sv_postflood_node');
                if ($rate_limit < 0)
                {
                    return;
                }
                else if ($rate_limit > 0)
                {
                    parent::assertNotFlooding('n'.$this->flood_nodeid, $rate_limit);
                    return;
                }
            }

            $rate_limit = $visitor->hasPermission('forum', 'sv_postflood_general');
            if ($rate_limit < 0)
            {
                return;
            }
            else if ($rate_limit > 0)
            {
                // do not user the $action, as that is shared with other stuff (ie reporting)
                parent::assertNotFlooding('post_', $rate_limit);
                return;
            }
        }
        parent::assertNotFlooding($action, $floodingLimit);
    }
}