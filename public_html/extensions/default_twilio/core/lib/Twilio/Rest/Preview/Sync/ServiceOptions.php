<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Preview\Sync;

use Twilio\Options;
use Twilio\Values;

/**
 * PLEASE NOTE that this class contains preview products that are subject to change. Use them with caution. If you currently do not have developer preview access, please contact help@twilio.com.
 */
abstract class ServiceOptions
{
    /**
     * @param string  $friendlyName                The friendly_name
     * @param string  $webhookUrl                  The webhook_url
     * @param boolean $reachabilityWebhooksEnabled The reachability_webhooks_enabled
     * @param boolean $aclEnabled                  The acl_enabled
     *
     * @return CreateServiceOptions Options builder
     */
    public static function create($friendlyName = Values::NONE, $webhookUrl = Values::NONE, $reachabilityWebhooksEnabled = Values::NONE, $aclEnabled = Values::NONE)
    {
        return new CreateServiceOptions($friendlyName, $webhookUrl, $reachabilityWebhooksEnabled, $aclEnabled);
    }

    /**
     * @param string  $webhookUrl                  The webhook_url
     * @param string  $friendlyName                The friendly_name
     * @param boolean $reachabilityWebhooksEnabled The reachability_webhooks_enabled
     * @param boolean $aclEnabled                  The acl_enabled
     *
     * @return UpdateServiceOptions Options builder
     */
    public static function update($webhookUrl = Values::NONE, $friendlyName = Values::NONE, $reachabilityWebhooksEnabled = Values::NONE, $aclEnabled = Values::NONE)
    {
        return new UpdateServiceOptions($webhookUrl, $friendlyName, $reachabilityWebhooksEnabled, $aclEnabled);
    }
}

class CreateServiceOptions extends Options
{
    /**
     * @param string  $friendlyName                The friendly_name
     * @param string  $webhookUrl                  The webhook_url
     * @param boolean $reachabilityWebhooksEnabled The reachability_webhooks_enabled
     * @param boolean $aclEnabled                  The acl_enabled
     */
    public function __construct($friendlyName = Values::NONE, $webhookUrl = Values::NONE, $reachabilityWebhooksEnabled = Values::NONE, $aclEnabled = Values::NONE)
    {
        $this->options['friendlyName'] = $friendlyName;
        $this->options['webhookUrl'] = $webhookUrl;
        $this->options['reachabilityWebhooksEnabled'] = $reachabilityWebhooksEnabled;
        $this->options['aclEnabled'] = $aclEnabled;
    }

    /**
     * The friendly_name
     *
     * @param string $friendlyName The friendly_name
     *
     * @return $this Fluent Builder
     */
    public function setFriendlyName($friendlyName)
    {
        $this->options['friendlyName'] = $friendlyName;
        return $this;
    }

    /**
     * The webhook_url
     *
     * @param string $webhookUrl The webhook_url
     *
     * @return $this Fluent Builder
     */
    public function setWebhookUrl($webhookUrl)
    {
        $this->options['webhookUrl'] = $webhookUrl;
        return $this;
    }

    /**
     * The reachability_webhooks_enabled
     *
     * @param boolean $reachabilityWebhooksEnabled The reachability_webhooks_enabled
     *
     * @return $this Fluent Builder
     */
    public function setReachabilityWebhooksEnabled($reachabilityWebhooksEnabled)
    {
        $this->options['reachabilityWebhooksEnabled'] = $reachabilityWebhooksEnabled;
        return $this;
    }

    /**
     * The acl_enabled
     *
     * @param boolean $aclEnabled The acl_enabled
     *
     * @return $this Fluent Builder
     */
    public function setAclEnabled($aclEnabled)
    {
        $this->options['aclEnabled'] = $aclEnabled;
        return $this;
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString()
    {
        $options = array();
        foreach ($this->options as $key => $value) {
            if ($value != Values::NONE) {
                $options[] = "$key=$value";
            }
        }
        return '[Twilio.Preview.Sync.CreateServiceOptions '.implode(' ', $options).']';
    }
}

class UpdateServiceOptions extends Options
{
    /**
     * @param string  $webhookUrl                  The webhook_url
     * @param string  $friendlyName                The friendly_name
     * @param boolean $reachabilityWebhooksEnabled The reachability_webhooks_enabled
     * @param boolean $aclEnabled                  The acl_enabled
     */
    public function __construct($webhookUrl = Values::NONE, $friendlyName = Values::NONE, $reachabilityWebhooksEnabled = Values::NONE, $aclEnabled = Values::NONE)
    {
        $this->options['webhookUrl'] = $webhookUrl;
        $this->options['friendlyName'] = $friendlyName;
        $this->options['reachabilityWebhooksEnabled'] = $reachabilityWebhooksEnabled;
        $this->options['aclEnabled'] = $aclEnabled;
    }

    /**
     * The webhook_url
     *
     * @param string $webhookUrl The webhook_url
     *
     * @return $this Fluent Builder
     */
    public function setWebhookUrl($webhookUrl)
    {
        $this->options['webhookUrl'] = $webhookUrl;
        return $this;
    }

    /**
     * The friendly_name
     *
     * @param string $friendlyName The friendly_name
     *
     * @return $this Fluent Builder
     */
    public function setFriendlyName($friendlyName)
    {
        $this->options['friendlyName'] = $friendlyName;
        return $this;
    }

    /**
     * The reachability_webhooks_enabled
     *
     * @param boolean $reachabilityWebhooksEnabled The reachability_webhooks_enabled
     *
     * @return $this Fluent Builder
     */
    public function setReachabilityWebhooksEnabled($reachabilityWebhooksEnabled)
    {
        $this->options['reachabilityWebhooksEnabled'] = $reachabilityWebhooksEnabled;
        return $this;
    }

    /**
     * The acl_enabled
     *
     * @param boolean $aclEnabled The acl_enabled
     *
     * @return $this Fluent Builder
     */
    public function setAclEnabled($aclEnabled)
    {
        $this->options['aclEnabled'] = $aclEnabled;
        return $this;
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString()
    {
        $options = array();
        foreach ($this->options as $key => $value) {
            if ($value != Values::NONE) {
                $options[] = "$key=$value";
            }
        }
        return '[Twilio.Preview.Sync.UpdateServiceOptions '.implode(' ', $options).']';
    }
}