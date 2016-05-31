<?php
	class Feedback
	{
		private $id;
		private $id_author;
		private $id_product;
		private $content;
		private $date;

		//GETTERS 
		public function getId()
		{
			return $this->id;
		}
		public function getAuthor()
		{
			return $this->id_author;
		}
		public function getProduct()
		{
			return $this->id_product;
		}
		public function getContent()
		{
			return $this->content;
		}
		public function getDate()
		{
			return $this->date;
		}
		public function getStatus()
		{
			return $this->status;		}

		//SETTERS
		public function setContent($content)
		{
			if (strlen($content) < 10)
				throw new Exception("Le contenu de votre avis est trop court!");
			if (strlen($content) > 255)
				throw new Exception("Le contenu de votre avis est trop long!");
			$this->content = $content;
		}
		public function setStatus($status)
		{
			if ($status >= 0 || $status <= 2)
				$this->status = $status;
			else
				throw new Exception("Mauvais Statut");
		}
	}
?>