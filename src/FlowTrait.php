<?php
/**
 * Created by PhpStorm.
 * Project: bes.131.im
 * Author: DeHua Chen
 * Email: x25125x@126.com
 * Date: 2019-04-12
 * Time: 15:56
 */

namespace OkamiChen\SymfonyEvent;

trait FlowTrait
{

    /**
     * @var \Symfony\Component\Workflow\Workflow
     */
    private $workflow;


    /**
     * @return \Symfony\Component\Workflow\Workflow
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getWorkflow()
    {
        if (!$this->workflow) {
            $this->workflow = Flow::getInstance($this->flowName ?? $this->table);
        }
        return $this->workflow;
    }

    /**
     * 获取工作流
     * @return array|\Symfony\Component\Workflow\Transition[]
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getEnabledTransitions()
    {
        return $this->getWorkflow()->getEnabledTransitions($this);
    }

    /**
     * 判断能否进入工作流
     * @param $transitionName 工作流名称
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */

    public function can($transitionName)
    {
        return $this->getWorkflow()->can($this, $transitionName);
    }

    /**
     * 进入工作流
     * @param $transitionName
     * @return \Symfony\Component\Workflow\Marking
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function apply($transitionName)
    {

        try {
            $marking = $this->getWorkflow()->apply($this, $transitionName);
            $this->save();
            return $marking;
        } catch (NotEnabledTransitionException $ex) {

            if (!$ex->getTransitionBlockerList()->has('custom')) {
                throw $ex;
            }

            $rows = $ex->getTransitionBlockerList();
            foreach ($rows as $key => $row) {
                throw new \Exception($row->getMessage(), $row->getCode());
            }
        }
    }

}
