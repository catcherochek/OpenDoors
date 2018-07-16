package tree;

import java.util.Iterator;
import java.util.NoSuchElementException;

// Introduction:
//
// This is an example class meant to illustrate several differen concepts:
//   * The use of type parameters (i.e. Java generics)
//   * Implementing an iterator over some collection, in this case an array
//   * Implementing the Iterable interface, which enables your collection
//     to work with the Java simple for loops, i.e. (for String s : list)
//
// To illustrate these concepts we implement a Java class that "wraps" a raw
// Java array and then provides some methods that the user can use to interact
// with it. It provides:
//
//   - get(index) to get the values from the array
//   - set(index, value) to set the value at a given index
//   - length() to return the size of the array
//   - iterator() which is needed by the Iterable interface
//
// This is not meant to be practically useful, but is meant be an illustrative
// example. It also shows how you can implement some of the same functionality
// that is provided by Java primitives in a way that you can extend them and
// add additional functionality (such as iteration).
//
// Note that this class contains a type parameter, shown in angle brackets
// alongside the class name. This is a feature of Java "generics" that enables
// an implementation to be re-used more easily.
//
// The way this works is Java lets you define a class without knowing the type
// you are going to work with, in this case we are calling this type "T". We
// write the class as usual, but where we would normally specify the type, such
// as String, Point, etc., we use "T" instead.
//
// When the user instantiates a new instance of the class, they must specify
// the type parameter like so:
//
//     Array<String> myArray = new Array<String>();
//
// This enables the compiler to check that the usage of the class is type-safe.
// In this example, let's take the get(int index) method, which returns the
// value at a given index. Assuming we're working with String values, we know
// that that method should return a string.
//
//     String first = myArray.get(0);
//
// Because Java knows we're working with an Array<String>, it can check to make
// sure that the type of the "first" variable and the value being returned by
// the get() method are compatible types.
//
// If we had declared myArray as follows instead:
//
//     Array<Integer> myArray = new Array<Integer>();
//
// Then the compiler would give us an error if we tried to assign the result
// of myArray.get(0) to a String.
//
// These type parameters allow us to write a class once and re-use it for
// multiple data types without having to create specialised versions for all
// different types. We would of course implement our own StringArray type
// which doesn't use type parameters/generics, but then we would have to copy
// and paste and change the types if we want to re-use that for another type.
//
// An example of how the Array class can be used it in the main method of this
// class.
public class Array<T> implements Iterable<T> {
    T[] values;  // this contains the actual elements of the array

    // Constructor that takes a "raw" array and stores it
    public Array(T[] values) {
        this.values = values;
    }

    // This is a private class that implements iteration over the elements
    // of the list. It is not accessed directly by the user, but is used in
    // the iterator() method of the Array class. It implements the hasNext()
    // and next() methods.
    class ArrayIterator implements Iterator<T> {
        int current = 0;  // the current element we are looking at

        // return whether or not there are more elements in the array that
        // have not been iterated over.
        public boolean hasNext() {
            if (current < Array.this.values.length) {
                return true;
            } else {
                return false;
            }
        }

        // return the next element of the iteration and move the current
        // index to the element after that.
        public T next() {
            if (!hasNext()) {
                throw new NoSuchElementException();
            }
            return values[current++];
        }
    }

    // Return the value at a given index
    public T get(int index) {
        return values[index];
    }

    // Set the value at a given index
    public void set(int index, T value) {
        values[index] = value;
    }

    // Return the length of the array
    public int length() {
        return values.length;
    }

    // Return an iterator over the elements in the array. This is generally not
    // called directly, but is called by Java when used in a "simple" for loop.
    public Iterator<T> iterator() {
        return new ArrayIterator();
    }

    // This is just a sample program that can be run to show how the Array
    // class might be used.
    public static void main(String[] args) {
        // create an array of strings
        String[] strings = new String[]{"Hello", "World"};

        // create a new array to hold these strings
        Array<String> array = new Array<String>(strings);

        // get and print the first values (prints "Hello")
        System.out.println(array.get(0));

        // set the second value
        array.set(1, "Javaland!");

        // iterate over the array, printing "Hello\nJavaland!"
        for (String s : array) {
            System.out.println(s);
        }
    }
}
