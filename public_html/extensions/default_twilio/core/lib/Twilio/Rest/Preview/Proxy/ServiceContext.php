<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Preview\Proxy;

use Twilio\Exceptions\TwilioException;
use Twilio\InstanceContext;
use Twilio\Options;
use Twilio\Rest\Preview\Proxy\Service\PhoneNumberList;
use Twilio\Rest\Preview\Proxy\Service\SessionList;
use Twilio\Rest\Preview\Proxy\Service\ShortCodeList;
use Twilio\Serialize;
use Twilio\Values;
use Twilio\Version;

/**
 * PLEASE NOTE that this class contains preview products that are subject to change. Use them with caution. If you currently do not have developer preview access, please contact help@twilio.com.
 *
 * @property \Twilio\Rest\Preview\Proxy\Service\SessionList     sessions
 * @property \Twilio\Rest\Preview\Proxy\Service\PhoneNumberList phoneNumbers
 * @property \Twilio\Rest\Preview\Proxy\Service\ShortCodeList   shortCodes
 * @method \Twilio\Rest\Preview\Proxy\Service\SessionContext sessions(string $sid)
 * @method \Twilio\Rest\Preview\Proxy\Service\PhoneNumberContext phoneNumbers(string $sid)
 * @method \Twilio\Rest\Preview\Proxy\Service\ShortCodeContext shortCodes(string $sid)
 */
class ServiceContext extends InstanceContext
{
    protected $_sessions = null;
    protected $_phoneNumbers = null;
    protected $_shortCodes = null;

    /**
     * Initialize the ServiceContext
     *
     * @param \Twilio\Version $version Version that contains the resource
     * @param string          $sid     A string that uniquely identifies this Service.
     *
     * @return \Twilio\Rest\Preview\Proxy\ServiceContext
     */
    public function __construct(Version $version, $sid)
    {
        parent::__construct($version);

        // Path Solution
        $this->solution = array('sid' => $sid,);

        $this->uri = '/Services/'.rawurlencode($sid).'';
    }

    /**
     * Fetch a ServiceInstance
     *
     * @return ServiceInstance Fetched ServiceInstance
     */
    public function fetch()
    {
        $params = Values::of(array());

        $payload = $this->version->fetch(
            'GET',
            $this->uri,
            $params
        );

        return new ServiceInstance($this->version, $payload, $this->solution['sid']);
    }

    /**
     * Deletes the ServiceInstance
     *
     * @return boolean True if delete succeeds, false otherwise
     */
    public function delete()
    {
        return $this->version->delete('delete', $this->uri);
    }

    /**
     * Update the ServiceInstance
     *
     * @param array|Options $options Optional Arguments
     *
     * @return ServiceInstance Updated ServiceInstance
     */
    public function update($options = array())
    {
        $options = new Values($options);

        $data = Values::of(array(
            'FriendlyName' => $options['friendlyName'],
            'AutoCreate'   => Serialize::booleanToString($options['autoCreate']),
            'CallbackUrl'  => $options['callbackUrl'],
        ));

        $payload = $this->version->update(
            'POST',
            $this->uri,
            array(),
            $data
        );

        return new ServiceInstance($this->version, $payload, $this->solution['sid']);
    }

    /**
     * Access the sessions
     *
     * @return \Twilio\Rest\Preview\Proxy\Service\SessionList
     */
    protected function getSessions()
    {
        if (!$this->_sessions) {
            $this->_sessions = new SessionList($this->version, $this->solution['sid']);
        }

        return $this->_sessions;
    }

    /**
     * Access the phoneNumbers
     *
     * @return \Twilio\Rest\Preview\Proxy\Service\PhoneNumberList
     */
    protected function getPhoneNumbers()
    {
        if (!$this->_phoneNumbers) {
            $this->_phoneNumbers = new PhoneNumberList($this->version, $this->solution['sid']);
        }

        return $this->_phoneNumbers;
    }

    /**
     * Access the shortCodes
     *
     * @return \Twilio\Rest\Preview\Proxy\Service\ShortCodeList
     */
    protected function getShortCodes()
    {
        if (!$this->_shortCodes) {
            $this->_shortCodes = new ShortCodeList($this->version, $this->solution['sid']);
        }

        return $this->_shortCodes;
    }

    /**
     * Magic getter to lazy load subresources
     *
     * @param string $name Subresource to return
     *
     * @return \Twilio\ListResource The requested subresource
     * @throws \Twilio\Exceptions\TwilioException For unknown subresources
     */
    public function __get($name)
    {
        if (property_exists($this, '_'.$name)) {
            $method = 'get'.ucfirst($name);
            return $this->$method();
        }

        throw new TwilioException('Unknown subresource '.$name);
    }

    /**
     * Magic caller to get resource contexts
     *
     * @param string $name      Resource to return
     * @param array  $arguments Context parameters
     *
     * @return \Twilio\InstanceContext The requested resource context
     * @throws \Twilio\Exceptions\TwilioException For unknown resource
     */
    public function __call($name, $arguments)
    {
        $property = $this->$name;
        if (method_exists($property, 'getContext')) {
            return call_user_func_array(array($property, 'getContext'), $arguments);
        }

        throw new TwilioException('Resource does not have a context');
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString()
    {
        $context = array();
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Preview.Proxy.ServiceContext '.implode(' ', $context).']';
    }
}