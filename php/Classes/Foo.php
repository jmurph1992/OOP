<?php

namespace Justin\OOP;

require_once("autoload.php");
require_once(dirname(__DIR__)."/vendor/autoload.php");
use Ramsey\Uuid\Uuid;

/**
 * Creates an object for the author. Inserts authorId, authorAvatarUrl,
 * authorActivationToken, authorEmail, authorHash, and authorUsername.
 *
 * @author Justin Murphy <jmurphy33@cnm.edu>
 *
 **/

class Author implements \JsonSerializable {
	use ValidateDate;
	use ValidateUuid;

	/**
	 * Primary key === authorId
	 **/
	private $authorId;

	/**
	 *URL for author
	 **/

	private $authorAvatarUrl;

	/**
	 *Activation token for author
	 **/

	private $authorActivationToken;

	/**
	 *author email
	 **/

	private $authorEmail;

	/**
	 *Hash for author
	 **/

	private $authorHash;

	/**
	 *author's username
	 **/

	private $authorUsername;

	/**
	 *constructor
	 *
	 * @param string|Uuid $authorId id for the author
	 * @param string $newAuthorAvatarUrl
	 * @param string $newAuthorActivationToken for security
	 * @param string $newAuthorEmail for email storage
	 * @param string $newAuthorHash for hashed password storage
	 * @param string $newAuthorUsername for storing the user name
	 * @throws \RangeException if entries are too long
	 * @throws \InvalidArgumentException if email address format is incorrect
	 * @throws \Exception if any other exception is found
	 * @throws \TypeError if data entered does not meet type requirements
	 **/

	public function __construct($newAuthorId, $newAuthorAvatarUrl, $newAuthorActivationToken, $newAuthorEmail,string $newAuthorHash, $newAuthorUsername) {
		try {
				$this->setAuthorId($newAuthorId);
				$this->setAuthorAvatarUrl($newAuthorAvatarUrl);
				$this->setAuthorActivationToken($newAuthorActivationToken);
				$this->setAuthorEmail($newAuthorEmail);
				$this->setAuthorHash($newAuthorHash);
				$this->setAuthorUsername($newAuthorUsername);
		}
		catch(\RangeException | \InvalidArgumentException | \Exception $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}

	}

	/**
	 *accessor method for authorId
	 *
	 * @return Uuid for author
	 **/

	public function getAuthorId() : Uuid {
		return($this->authorId);
	}

	/**
	 *mutator for authorId
	 *
	 * @param Uuid | string $newAuthorId new value of author id
	 * @throws \TypeError if $newAuthorId is not a UUID or string
	 * @throws \Exception if any other error is found
	 **/

	public function setAuthorId($newAuthorId) : void {
		try {
				$newUuid = self::validateUuid($newAuthorId);
		}	catch(\TypeError | \Exception $exception) {
				$exceptionType = get_class($exception);
				throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
		//stores new uuid
		$this->authorId = $newUuid;
	}

	/**
	 *accessor method for author avatar url
	 *
	 * @return string for url
	 **/

	public function getAuthorAvatarUrl() : string {
		return ($this->authorAvatarUrl);
	}

	/**
	 *mutator for author avatar url
	 *
	 * @param string $newAuthorAvatarEmail new value of author avatar url
	 * @throws \TypeError if string entered does not meet field requirements
	 * @throws \Exception if any other error is found
	 **/

	public function setAuthorAvatarUrl(string $newAuthorAvatarUrl) : void {

		$newAuthorAvatarUrl = trim($newAuthorAvatarUrl);
		$newAuthorAvatarUrl = filter_var($newAuthorAvatarUrl, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		//verify avatar url fits in database
		if(strlen($newAuthorAvatarUrl) > 255) {
			throw(new \RangeException("Image content too large"));
		}
		//store the image
		$this->authorAvatarUrl = $newAuthorAvatarUrl;
	}

	/**
	 *accessor method for author activation token
	 *
	 * @return string for token
	 **/

	public function getAuthorActivationToken() : ?string {
		return ($this->authorActivationToken);
	}

	/**
	 *mutator for author activation token
	 *
	 * @param string $newAuthorActivationToken new value of activation token
	 * @throws \RangeException if string entered does not meet field requirements
	 * @throws \Exception if any other error is found
	 **/

	public function setAuthorActivationToken(?string $newAuthorActivationToken) : void {

		if($newAuthorActivationToken === null) {
			$this->authorActivationToken = null;
			return;
		}

		$newAuthorActivationToken = strtolower(trim($newAuthorActivationToken));
		if(ctype_xdigit($newAuthorActivationToken) === false) {
			throw(new \RangeException("Activation token not valid"));
		}

		if(strlen($newAuthorActivationToken) !== 32) {
			throw(new \RangeException("Activation token must be 32 characters"));
		}

		$this->authorActivationToken = $newAuthorActivationToken;
	}

	/**
	 *accessor method for the author email
	 *
	 * @return string for email
	 **/

	public function getAuthorEmail() : string {
		return ($this->authorEmail);
	}

	/**
	 *mutator method for author email
	 *
	 * @param string new value of email
	 * @throws \InvalidArgumentException if $newAuthorEmail is not a valid string
	 * @throws \RangeException if email is too long
	 * @throws \Exception if any other error is found	 *
	 **/

	public function setAuthorEmail(string $newAuthorEmail) : void {

		//verifies email security
		$newAuthorEmail = trim($newAuthorEmail);
		$newAuthorEmail = filter_var($newAuthorEmail, FILTER_VALIDATE_EMAIL);
		if(empty($newAuthorEmail) === true) {
			throw(new \InvalidArgumentException("email is insecure or invalid"));
		}

		//checks if email is too large for database
		if(strlen($newAuthorEmail) > 128) {
			throw(new \RangeException("email is too large"));
		}

		$this->authorEmail = $newAuthorEmail;
	}

	/**
	 * accessor method for profile hash
	 *
	 * @return value of hash
	 **/

	public function getAuthorHash() : string {
		return ($this->authorHash);
	}

	/**
	 * mutator method for authorHash
	 *
	 * @param string $newAuthorHash
	 * @throws \InvalidArgumentException if hash is not secure
	 * @throws \RangeException if hash is not proper length
	 * @throws \TypeError if hash is not a string
	 **/

	public function setAuthorHash(string $newAuthorHash) : void {

		$newAuthorHash = trim($newAuthorHash);
		if(empty($newAuthorHash) === true) {
			throw(new \InvalidArgumentException("hash field is empty or insecure"));
		}

		$hashInfo = password_get_info($newAuthorHash);
		if($hashInfo["algoName"] !== "argon2i") {
			throw(new \InvalidArgumentException("Author hash is not valid"));

		}

		if (strlen($newAuthorHash) !== 97) {
			throw(new \RangeException("Profile hash must be 97 characters"));
		}

		$this->authorHash = $newAuthorHash;
	}

	/**
	 * accessor method for authorUsername
	 *
	 * @return string for author user name
	 **/

	public function getAuthorUsername() : string {
		return ($this->authorUsername);
	}

	/**
	 * mutator method for authorUsername
	 *
	 * @param string $newAuthorUsername
	 * @throws \InvalidArgumentException if user name is not a string
	 * @throws \RangeException if new user name is longer than 32 characters
	 * @throws \TypeError if user name is not a string
	 **/

	public function setAuthorUsername(string $newAuthorUsername) : void {

		$newAuthorUsername = trim($newAuthorUsername);
		$newAuthorUsername = filter_var($newAuthorUsername, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newAuthorUsername) === true) {
			throw(new \InvalidArgumentException("username is empty or taken"));
		}

		//checks length of username
		if(strlen($newAuthorUsername) > 32) {
			throw(new \RangeException("username is too long"));
		}

		$this->authorUsername = $newAuthorUsername;
	}

	/**
	 * Inserts Author and author info into MySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param \PDOException when MySQL related error occurs
	 * @param \TypeError if $pdo is not a PDO connection object
	 **/

	public function insert(\PDO $pdo) : void {

		//create query template
		$query = "INSERT INTO author(authorId, authorAvatarUrl, authorActivationToken, authorEmail, authorHash, authorUsername) 
						VALUES (:authorId, :authorAvatarUrl, :authorActivationToken, :authorEmail, :authorHash, :authorUsername)";
		$statement = $pdo->prepare($query);

		$parameters = ["authorId" => $this->authorId->getBytes(),"authorAvatarUrl" =>$this->authorAvatarUrl,"authorActivationToken" =>$this->authorActivationToken,
								"authorEmail" =>$this->authorEmail, "authorHash" =>$this->authorHash, "authorUsername" =>$this->authorUsername];
		$statement -> execute($parameters);
	}

	/**
	 * deletes this author from MySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOExceptionwhen MySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/

	public function delete(\PDO %pdo): void {

		//create query template

		$query = "DELETE FROM author WHERE authorId = :authorId";
		$statement = $pdo->prepare($query);

		//bind the member variables to the place holders
		$parameters = ["authorId" => $this -> authorId -> getBytes()];
		$statement -> execute
}


	/**
	 * Specify data which should be serialized to JSON
	 * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize() {
		$fields = get_object_vars($this);
		$fields["authorId"] = $this->authorId->toString();
		unset($fields["authorActivationToken"]);
		unset($fields["authorHash"]);
		return ($fields);
	}
}
