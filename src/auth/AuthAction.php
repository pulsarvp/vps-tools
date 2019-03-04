<?php

	namespace vps\tools\auth;

	use Yii;
	use yii\authclient\AuthAction as BaseAuthAction;
	use yii\base\Exception;
	use yii\web\NotFoundHttpException;

	/**
	 * AuthAction performs authentication via different auth clients.
	 */
	class AuthAction extends BaseAuthAction
	{

		/**
		 * @inheritdoc
		 */
		public function run ()
		{

			if (!empty($_GET[ $this->clientIdGetParamName ]))
			{
				$clientId = $_GET[ $this->clientIdGetParamName ];
				Yii::$app->session->set('client', $clientId);
			}
			else
			{
				$clientId = Yii::$app->session->get('client');
			}
			if (!empty($clientId))
			{
				$collection = Yii::$app->get($this->clientCollection);
				if (!$collection->hasClient($clientId))
				{
					throw new NotFoundHttpException("Unknown auth client '{$clientId}'");
				}
				$client = $collection->getClient($clientId);

				return $this->auth($client);
			}

			throw new NotFoundHttpException("Auth client not found.");
		}

		/** @inheritdoc */
		protected function authOAuth2 ($client, $authUrlParams = [])

		{
			$get = Yii::$app->request->get();
			if (isset($get[ 'error' ]))
			{
				if ($get[ 'error' ] == 'access_denied')
				{
					$this->redirectCancel();
					Yii::$app->end();
				}
				else
				{
					// request error
					if (isset($get[ 'error_description' ]))
					{
						$errorMessage = $get[ 'error_description' ];
					}
					elseif (isset($get[ 'error_message' ]))
					{
						$errorMessage = $get[ 'error_message' ];
					}
					else
					{
						$errorMessage = http_build_query($get);
					}
					throw new Exception('Auth error: ' . $errorMessage);
				}
			}

			// Get the access_token and save them to the session.
			if (isset($get[ 'code' ]))
			{
				$code = $get[ 'code' ];
				$token = $client->fetchAccessToken($code);
				Yii::$app->session->remove('client');
				if (!empty($token))
				{
					return $this->authSuccess($client);
				}
				else
				{
					return $this->redirectCancel();
				}
			}
			else
			{
				$url = $client->buildAuthUrl($authUrlParams);
				Yii::$app->response->redirect($url);
				Yii::$app->end();
			}
		}
	}
