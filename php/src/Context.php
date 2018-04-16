<?php

namespace Twirp;

/**
 * Context key constants and access logic.
 */
final class Context
{
    const METHOD_NAME = 'method_name';
    const SERVICE_NAME = 'service_name';
    const PACKAGE_NAME = 'package_name';
	const STATUS_CODE = 'status_code';
	const REQUEST_HEADER = 'request_header_key';
	const RESPONSE_HEADER = 'response_header_key';

    /**
     * Extracts the name of the method being handled in the given
     * context. If it is not known, it returns null.
     *
     * @param array $ctx
     *
     * @return string
     */
    public static function methodName(array $ctx)
    {
        if (isset($ctx[self::METHOD_NAME])) {
            return $ctx[self::METHOD_NAME];
        }

        return null;
    }

    /**
     * Sets the method name in the context.
     *
     * @param array  $ctx
     * @param string $name
     *
     * @return array
     */
    public static function withMethodName(array $ctx, $name)
    {
        $ctx[self::METHOD_NAME] = $name;

        return $ctx;
    }

    /**
     * Extracts the name of the service handling the given context. If
     * it is not known, it returns null.
     *
     * @param array $ctx
     *
     * @return string
     */
    public static function serviceName(array $ctx)
    {
        if (isset($ctx[self::SERVICE_NAME])) {
            return $ctx[self::SERVICE_NAME];
        }

        return null;
    }

    /**
     * Sets the service name in the context.
     *
     * @param array  $ctx
     * @param string $name
     *
     * @return array
     */
    public static function withServiceName(array $ctx, $name)
    {
        $ctx[self::SERVICE_NAME] = $name;

        return $ctx;
    }

    /**
     * Extracts the fully-qualified protobuf package name of the service
     * handling the given context. If it is not known, it returns null. If
     * the service comes from a proto file that does not declare a package name, it
     * returns "".
     *
     * Note that the protobuf package name can be very different than the go package
     * name; the two are unrelated.
     *
     * @param array $ctx
     *
     * @return string
     */
    public static function packageName(array $ctx)
    {
        if (isset($ctx[self::PACKAGE_NAME])) {
            return $ctx[self::PACKAGE_NAME];
        }

        return null;
    }

    /**
     * Sets the package name in the context.
     *
     * @param array  $ctx
     * @param string $name
     *
     * @return array
     */
    public static function withPackageName(array $ctx, $name)
    {
        $ctx[self::PACKAGE_NAME] = $name;

        return $ctx;
    }

    /**
     * Retrieves the status code of the response (as string like "200").
     * If it is known returns the status.
     * If it is not known, it returns null.
     *
     * @param array $ctx
     *
     * @return int
     */
    public static function statusCode(array $ctx)
    {
        if (isset($ctx[self::STATUS_CODE])) {
            return $ctx[self::STATUS_CODE];
        }

        return null;
    }

    /**
     * Sets the status code in the context.
     *
     * @param array $ctx
     * @param int   $code
     *
     * @return array
     */
    public static function withStatusCode(array $ctx, $code)
    {
        $ctx[self::STATUS_CODE] = $code;

        return $ctx;
    }

	/**
	 * Retrieves the HTTP headers sent as part of the request.
	 * If there are no headers, it returns an empty array.
	 *
	 * @param array $ctx
	 *
	 * @return array
	 */
	public static function httpRequestHeaders(array $ctx)
	{
		if (isset($ctx[self::REQUEST_HEADER])) {
			return $ctx[self::REQUEST_HEADER];
		}

		return [];
	}

	/**
	 * Stores an HTTP headers in a context. When
	 * using a Twirp-generated client, you can pass the returned context
	 * into any of the request methods, and the stored header will be
	 * included in outbound HTTP requests.
	 *
	 * This can be used to set custom HTTP headers like authorization tokens or
	 * client IDs. But note that HTTP headers are a Twirp implementation detail,
	 * only visible by middleware, not by the server implementation.
	 *
	 * Throws an exception if the provided headers
	 * would overwrite a header that is needed by Twirp, like "Content-Type".
	 *
	 *
	 * @param array $ctx
	 * @param array $headers
	 *
	 * @return array
	 *
	 * @throws \InvalidArgumentException when any of the following headers are included: Accept, Content-Type, Twirp-Version
	 */
	public static function withHttpRequestHeaders(array $ctx, array $headers)
	{
		foreach ($headers as $key => $value) {
			$key = strtolower($key);
			$msg = 'provided header cannot set %s';

			switch ($key) {
				case 'accept':
					throw new \InvalidArgumentException(sprintf($msg, 'Accept'));

				case 'content-type':
					throw new \InvalidArgumentException(sprintf($msg, 'Content-Type'));

				case 'twirp-version':
					throw new \InvalidArgumentException(sprintf($msg, 'Twirp-Version'));
			}
		}

		$ctx[self::REQUEST_HEADER] = $headers;

		return $ctx;
	}
}
