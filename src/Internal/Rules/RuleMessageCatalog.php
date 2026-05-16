<?php

namespace HongXunPan\Validator\Internal\Rules;

class RuleMessageCatalog
{
    /**
     * @var array
     */
    private $messageMap = array();

    /**
     * @param array<string, string> $messages
     *
     * @return void
     */
    public function registerMessages(array $messages)
    {
        foreach ($messages as $ruleKey => $template) {
            $this->messageMap[(string)$ruleKey] = (string)$template;
        }
    }

    /**
     * @param ResolvedRule $resolvedRule
     *
     * @return string
     */
    public function resolveMessage(ResolvedRule $resolvedRule)
    {
        $finalRuleKey = $resolvedRule->finalRuleKey();
        $ruleClass = $resolvedRule->ruleClass();

        if (array_key_exists($finalRuleKey, $this->messageMap)) {
            return $this->messageMap[$finalRuleKey];
        }

        $message = call_user_func(array($ruleClass, 'defaultMessage'));

        return $message === null || $message === ''
            ? RuleMessageTemplates::defaultTemplate()
            : $message;
    }
}
