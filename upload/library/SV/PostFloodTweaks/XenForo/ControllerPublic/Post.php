<?php

class SV_PostFloodTweaks_XenForo_ControllerPublic_Post extends XFCP_SV_PostFloodTweaks_XenForo_ControllerPublic_Post
{
    protected function likeFloodCheck()
    {
        $visitor = XenForo_Visitor::getInstance();
        if (!$visitor->hasPermission('general', 'bypassFloodCheck'))
        {
            $postId = $this->_input->filterSingle('post_id', XenForo_Input::UINT);

            $ftpHelper = $this->getHelper('ForumThreadPost');
            $postFetchOptions = array('readUserId' => $visitor['user_id']);
            $threadFetchOptions = array('readUserId' => $visitor['user_id']);
            $forumFetchOptions = array('readUserId' => $visitor['user_id']);
            list($post, $thread, $forum) = $ftpHelper->assertPostValidAndViewable($postId, $postFetchOptions, $threadFetchOptions, $forumFetchOptions);

            if (isset($thread['thread_id']))
            {
                $threadid = $thread['thread_id'];
            }
            if (isset($forum['node_id']))
            {
                $nodeid = $forum['node_id'];
            }

            if (!empty($threadid) && $visitor->hasPermission('forum', 'sv_likeflood_thread_on'))
            {
                $rate_limit = $visitor->hasPermission('forum', 'sv_likeflood_thread');
                if ($rate_limit < 0)
                {
                    return;
                }
                else if ($rate_limit > 0)
                {
                    $this->assertNotFlooding('lt'.$threadid, $rate_limit);
                    return;
                }
            }

            if (!empty($nodeid) && $visitor->hasPermission('forum', 'sv_likeflood_node_on'))
            {
                $rate_limit = $visitor->hasPermission('forum', 'sv_likeflood_node');
                if ($rate_limit < 0)
                {
                    return;
                }
                else if ($rate_limit > 0)
                {
                    $this->assertNotFlooding('ln'.$nodeid, $rate_limit);
                    return;
                }
            }

            $rate_limit = $visitor->hasPermission('forum', 'sv_likeflood_general');
            if ($rate_limit < 0)
            {
                return;
            }
            else if ($rate_limit > 0)
            {
                $this->assertNotFlooding('like', $rate_limit);
                return;
            }
        }
    }

    public function actionRate()
    {
        if ($this->_request->isPost())
        {
            $this->likeFloodCheck();
        }

        return parent::actionRate();
    }

    public function actionLike()
    {
        if ($this->_request->isPost())
        {
            $this->likeFloodCheck();
        }

        return parent::actionLike();
    }
}