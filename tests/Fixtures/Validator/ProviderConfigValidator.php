<?php

namespace HongXunPan\Validator\Tests\Fixtures\Validator;

use HongXunPan\Validator\Tests\Fixtures\ValidatorConfig\ProviderExtraRules;
use HongXunPan\Validator\Tests\Fixtures\ValidatorConfig\ProviderRuleAliases;
use HongXunPan\Validator\Tests\Fixtures\ValidatorConfig\ProviderRuleMessages;
use HongXunPan\Validator\Validator;

class ProviderConfigValidator extends Validator
{
    const EXTRA_RULES_PROVIDER_CLASS = ProviderExtraRules::class;
    const RULE_ALIASES_PROVIDER_CLASS = ProviderRuleAliases::class;
    const RULE_MESSAGES_PROVIDER_CLASS = ProviderRuleMessages::class;
}
