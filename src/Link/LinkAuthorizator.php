<?php

declare(strict_types=1);

namespace SixtyEightPublishers\SmartNetteComponent\Link;

use Nette;
use SixtyEightPublishers;

final class LinkAuthorizator implements ILinkAuthorizator
{
	use Nette\SmartObject;

	/** @var \SixtyEightPublishers\SmartNetteComponent\Reader\IAnnotationReader  */
	private $annotationReader;

	/** @var \Nette\Security\User  */
	private $user;

	/** @var array  */
	private $cache = [];

	/**
	 * @param \SixtyEightPublishers\SmartNetteComponent\Reader\IAnnotationReader $annotationReader
	 * @param \Nette\Security\User                                               $user
	 */
	public function __construct(SixtyEightPublishers\SmartNetteComponent\Reader\IAnnotationReader $annotationReader, Nette\Security\User $user)
	{
		$this->annotationReader = $annotationReader;
		$this->user = $user;
	}

	/**
	 * @param object $annotation
	 *
	 * @return bool
	 */
	private function checkAnnotation($annotation): bool
	{
		if ($annotation instanceof SixtyEightPublishers\SmartNetteComponent\Annotation\IAuthorizationAnnotation) {
			return $annotation->isAllowed($this->user);
		}

		return TRUE;
	}

	/**
	 * @param \ReflectionClass $reflectionClass
	 *
	 * @return bool
	 */
	private function resolveClass(\ReflectionClass $reflectionClass): bool
	{
		foreach ($this->annotationReader->getClassAnnotations($reflectionClass) as $classAnnotation) {
			if (FALSE === $this->checkAnnotation($classAnnotation->getAnnotation())) {
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * @param \ReflectionMethod $reflectionMethod
	 *
	 * @return bool
	 */
	private function resolveMethod(\ReflectionMethod $reflectionMethod): bool
	{
		foreach ($this->annotationReader->getMethodAnnotations($reflectionMethod) as $methodAnnotation) {
			if (FALSE === $this->checkAnnotation($methodAnnotation)) {
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * @param string $presenterClassName
	 * @param string $action
	 *
	 * @return bool
	 */
	private function resolveAction(string $presenterClassName, string $action): bool
	{
		$reflection = new \ReflectionClass($presenterClassName);

		if (FALSE === $this->resolveClass($reflection)) {
			return FALSE;
		}

		if (TRUE === $reflection->hasMethod($action = Nette\Application\UI\Presenter::formatActionMethod($action))
			&& FALSE === $this->resolveMethod($reflection->getMethod($action))) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @param string $controlClassName
	 * @param string $signal
	 * @param bool   $checkClass
	 *
	 * @return bool
	 */
	private function resolveSignal(string $controlClassName, string $signal, bool $checkClass): bool
	{
		if (TRUE === $checkClass && FALSE === $this->resolveClass($reflection = new \ReflectionClass($controlClassName))) {
			return FALSE;
		}

		$signal = Nette\Application\UI\Component::formatSignalMethod($signal);
		$reflection = isset($reflection) ? $reflection->getMethod($signal) : new \ReflectionMethod($controlClassName, $signal);

		return $this->resolveMethod($reflection);
	}

	/********************** interface \SixtyEightPublishers\SmartNetteComponent\Link\ILinkAuthorizator **********************/

	/**
	 * {@inheritdoc}
	 */
	public function isActionAllowed(string $presenterClassName, string $action = 'default'): bool
	{
		if (array_key_exists($key = sprintf('%s#%s', $presenterClassName, $action), $this->cache)) {
			return $this->cache[$key];
		}

		if (!is_subclass_of($presenterClassName, SixtyEightPublishers\SmartNetteComponent\UI\Presenter::class, TRUE)) {
			return $this->cache[$key] = TRUE;
		}

		return $this->cache[$key] = $this->resolveAction($presenterClassName, $action);
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSignalAllowed(string $controlClassName, string $signal): bool
	{
		$signal = rtrim($signal, '!');

		if (array_key_exists($key = sprintf('%s#%s!', $controlClassName, $signal), $this->cache)) {
			return $this->cache[$key];
		}

		if (!($isPresenter = is_subclass_of($controlClassName, SixtyEightPublishers\SmartNetteComponent\UI\Presenter::class, TRUE))
			&& !is_subclass_of($controlClassName, SixtyEightPublishers\SmartNetteComponent\UI\Control::class, TRUE)) {
			return $this->cache[$key] = TRUE;
		}

		return $this->cache[$key] = $this->resolveSignal($controlClassName, $signal, $isPresenter);
	}
}
